@extends('layouts.guest')

@section('title', 'Thực đơn — PND Restaurant')

@section('page-styles')
<style>
    /* ── Page hero ── */
    .page-hero {
        padding: 160px 24px 80px;
        position: relative;
        overflow: hidden;
    }
    .page-hero::before {
        content: '';
        position: absolute; inset: 0;
        background: radial-gradient(ellipse at 60% 100%, rgba(234,107,30,0.1) 0%, transparent 60%);
    }
    .page-hero-text {
        font-family: 'Noto Serif', serif;
        font-size: clamp(3rem, 7vw, 5.5rem);
        font-weight: 700; line-height: 1.05;
        letter-spacing: -0.02em;
    }

    /* ── Filter tabs ── */
    .filter-bar {
        position: sticky; top: 80px; z-index: 30;
        background: rgba(18,20,18,0.9);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid #584238;
        padding: 0 24px;
    }
    .filter-tab {
        display: inline-flex; align-items: center; gap-6px;
        padding: 18px 0;
        margin-right: 32px;
        font-family: 'Work Sans', sans-serif;
        font-size: 0.75rem; font-weight: 600;
        letter-spacing: 0.12em; text-transform: uppercase;
        color: #a78b7e;
        border-bottom: 2px solid transparent;
        text-decoration: none;
        white-space: nowrap;
        transition: color 0.2s, border-color 0.2s;
    }
    .filter-tab:hover { color: #ffb693; }
    .filter-tab.active { color: #ffb693; border-bottom-color: #ea6b1e; }

    /* ── Section heading ── */
    .cat-heading {
        font-family: 'Noto Serif', serif;
        font-size: clamp(1.8rem, 4vw, 2.5rem);
        font-weight: 700;
        color: #e3e2e0;
        padding-bottom: 16px;
        border-bottom: 1px solid #584238;
        margin-bottom: 32px;
        display: flex; align-items: center; justify-content: space-between;
    }
    .cat-count {
        font-family: 'Work Sans', sans-serif;
        font-size: 0.75rem; font-weight: 600;
        color: #ea6b1e; letter-spacing: 0.15em;
        background: rgba(234,107,30,0.1);
        border: 1px solid rgba(234,107,30,0.3);
        padding: 4px 12px;
    }

    /* ── Menu card ── */
    .menu-card {
        background: #1f201e;
        border: 1px solid #584238;
        overflow: hidden;
        transition: border-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
    }
    .menu-card:hover {
        border-color: rgba(255,182,147,0.6);
        transform: translateY(-5px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    }
    .menu-card-img-wrap { overflow: hidden; position: relative; }
    .menu-card-img {
        width: 100%; aspect-ratio: 16/10;
        object-fit: cover;
        display: block;
        background: #292a29;
        transition: transform 0.6s ease;
    }
    .menu-card:hover .menu-card-img { transform: scale(1.07); }
    .menu-card-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(18,20,18,0.6) 0%, transparent 50%);
        opacity: 0; transition: opacity 0.3s;
    }
    .menu-card:hover .menu-card-overlay { opacity: 1; }

    .price-badge {
        font-family: 'Noto Serif', serif;
        font-size: 1.15rem; font-weight: 700;
        letter-spacing: 0.05em;
        color: #ffb693;
    }

    /* ── Empty state ── */
    .empty-state {
        text-align: center; padding: 80px 24px;
        border: 1px dashed #584238;
    }

    /* ── Reveal ── */
    .reveal {
        opacity: 0; transform: translateY(24px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .reveal.visible { opacity: 1; transform: none; }

    /* ── Filter tabs scroll ── */
    .filter-scroll {
        overflow-x: auto;
        -ms-overflow-style: none;
        scrollbar-width: none;
        display: flex;
    }
    .filter-scroll::-webkit-scrollbar { display: none; }
</style>
@endsection

@section('content')

{{-- ── Page Hero ── --}}
<div class="page-hero mt-12">
    <div class="max-w-[1200px] mx-auto relative z-10">
        <div class="flex items-center gap-3 mb-6 reveal">
            <div class="h-px w-10 bg-primary-container"></div>
            <span class="text-primary-container font-sans text-xs font-bold tracking-[0.2em] uppercase">Thực đơn</span>
        </div>
        <h1 class="page-hero-text text-on-surface mb-4 reveal" style="transition-delay:0.1s">
            Tinh hoa<br><span style="color:#ffb693">bếp PND</span>
        </h1>
        <p class="text-on-surface-variant max-w-xl leading-relaxed reveal" style="transition-delay:0.2s">
            Mỗi món ăn là một hành trình cảm xúc — từ nguyên liệu được tuyển chọn kỹ lưỡng 
            đến bàn tay điêu luyện của đội ngũ bếp trưởng 5 sao.
        </p>
    </div>
</div>

{{-- ── Filter Bar ── --}}
<div class="filter-bar">
    <div class="max-w-[1200px] mx-auto">
        <div class="filter-scroll">
            <a href="{{ route('guest.menu') }}"
               class="filter-tab {{ !$selectedCategory ? 'active' : '' }}">
                Tất cả
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('guest.menu', ['category' => $cat->slug]) }}"
                   class="filter-tab {{ $selectedCategory === $cat->slug ? 'active' : '' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Main Content ── --}}
<div class="max-w-[1200px] mx-auto px-6 py-16">

    @if($selectedCategory)
        {{-- ── Single category filtered view ── --}}
        @php $cat = $categories->firstWhere('slug', $selectedCategory); @endphp
        @if($cat && $cat->description)
            <p class="text-on-surface-variant mb-10 max-w-2xl leading-relaxed reveal">{{ $cat->description }}</p>
        @endif

        @if($menuItems->isEmpty())
            <div class="empty-state reveal">
                <span class="material-symbols-outlined text-4xl text-outline mb-4 block">restaurant_menu</span>
                <p class="text-on-surface-variant font-sans">Chưa có món ăn nào trong danh mục này.</p>
            </div>
        @else
            <div class="cat-heading reveal">
                <span>{{ $cat?->name ?? 'Thực đơn' }}</span>
                <span class="cat-count">{{ $menuItems->count() }} món</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($menuItems as $i => $item)
                    @include('guest._menu_card', ['item' => $item, 'delay' => $i * 0.07])
                @endforeach
            </div>
        @endif

    @else
        {{-- ── All categories view ── --}}
        @forelse($categories as $catIdx => $category)
            @if($category->menuItems->isNotEmpty())
                <div class="mb-20">
                    <div class="cat-heading reveal" style="transition-delay:{{ $catIdx * 0.05 }}s">
                        <span>{{ $category->name }}</span>
                        <span class="cat-count">{{ $category->menuItems->count() }} món</span>
                    </div>

                    @if($category->description)
                        <p class="text-on-surface-variant mb-8 max-w-2xl leading-relaxed -mt-6 reveal">
                            {{ $category->description }}
                        </p>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($category->menuItems as $i => $item)
                            @include('guest._menu_card', ['item' => $item, 'delay' => $i * 0.07])
                        @endforeach
                    </div>
                </div>
            @endif
        @empty
            <div class="empty-state reveal">
                <span class="material-symbols-outlined text-4xl text-outline mb-4 block">restaurant_menu</span>
                <p class="text-on-surface-variant font-sans">Thực đơn đang được cập nhật. Vui lòng quay lại sau.</p>
            </div>
        @endforelse
    @endif

</div>

{{-- ── Banner CTA ── --}}
<section class="border-t border-outline-variant py-20 px-6"
         style="background:linear-gradient(135deg,rgba(234,107,30,0.07) 0%,transparent 60%),#0d0f0d">
    <div class="max-w-[1200px] mx-auto flex flex-col md:flex-row items-center justify-between gap-8 reveal">
        <div>
            <h3 class="font-serif text-2xl font-bold text-on-surface mb-2">Muốn trải nghiệm không gian riêng?</h3>
            <p class="text-on-surface-variant text-sm">Đặt bàn private dining cho buổi tối đặc biệt của bạn.</p>
        </div>
        <a href="{{ route('guest.bookings.index') }}" class="btn-cta shrink-0">
            Đặt bàn ngay
        </a>
    </div>
</section>

@endsection

@section('scripts')
<script>
    const revealEls = document.querySelectorAll('.reveal');
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); }
        });
    }, { threshold: 0.1 });
    revealEls.forEach(el => io.observe(el));
    // Trigger top elements
    document.querySelectorAll('.page-hero .reveal').forEach((el,i) => {
        setTimeout(() => el.classList.add('visible'), 100 + i*150);
    });
</script>
@endsection