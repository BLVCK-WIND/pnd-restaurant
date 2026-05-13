@extends('layouts.guest')

@section('title', 'Trang chủ — PND Restaurant')

@section('page-styles')
<style>
    /* ── Hero ── */
    .hero-section {
        min-height: 100vh;
        position: relative;
        display: flex;
        align-items: center;
        overflow: hidden;
    }
    .hero-bg {
        position: absolute; inset: 0;
        background:
            linear-gradient(135deg, rgba(18,20,18,0.97) 0%, rgba(18,20,18,0.75) 50%, rgba(18,20,18,0.4) 100%),
            radial-gradient(ellipse at 70% 50%, rgba(234,107,30,0.12) 0%, transparent 60%);
        z-index: 1;
    }
    .hero-grid {
        position: absolute; inset: 0; z-index: 0;
        background-image:
            linear-gradient(rgba(88,66,56,0.15) 1px, transparent 1px),
            linear-gradient(90deg, rgba(88,66,56,0.15) 1px, transparent 1px);
        background-size: 60px 60px;
        mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
    }
    .hero-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        animation: orbFloat 8s ease-in-out infinite;
        pointer-events: none;
    }
    @keyframes orbFloat {
        0%,100% { transform: translateY(0) scale(1); }
        50%      { transform: translateY(-30px) scale(1.05); }
    }

    /* ── Reveal animations ── */
    .reveal {
        opacity: 0;
        transform: translateY(32px);
        transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .reveal.visible { opacity: 1; transform: none; }

    /* ── Category pills ── */
    .cat-pill {
        display: inline-flex; align-items: center; gap: 6px;
        border: 1px solid #584238;
        padding: 8px 20px;
        font-family: 'Work Sans', sans-serif;
        font-size: 0.75rem; font-weight: 600;
        letter-spacing: 0.12em; text-transform: uppercase;
        color: #e0c0b2;
        text-decoration: none;
        transition: all 0.25s ease;
        position: relative; overflow: hidden;
    }
    .cat-pill::before {
        content: '';
        position: absolute; inset: 0;
        background: var(--primary-container);
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.25s ease;
        z-index: 0;
    }
    .cat-pill:hover::before { transform: scaleX(1); }
    .cat-pill:hover { color: #4b1b00; border-color: var(--primary-container); }
    .cat-pill span { position: relative; z-index: 1; }

    /* ── Menu item card ── */
    .menu-card {
        background: #1f201e;
        border: 1px solid #584238;
        overflow: hidden;
        transition: border-color 0.3s, transform 0.3s;
        position: relative;
    }
    .menu-card:hover {
        border-color: #ffb693;
        transform: translateY(-4px);
    }
    .menu-card-img {
        width: 100%; aspect-ratio: 4/3;
        object-fit: cover;
        transition: transform 0.6s ease;
        display: block;
        background: #292a29;
    }
    .menu-card:hover .menu-card-img { transform: scale(1.06); }
    .menu-card-badge {
        position: absolute; top: 12px; left: 12px;
        background: var(--primary-container);
        color: #4b1b00;
        font-size: 0.65rem; font-weight: 700;
        letter-spacing: 0.12em; text-transform: uppercase;
        padding: 3px 10px;
    }
    .price-tag {
        font-family: 'Noto Serif', serif;
        font-size: 1.1rem; font-weight: 600;
        letter-spacing: 0.05em;
        color: #ffb693;
    }

    /* ── Testimonials ── */
    .testimonial-card {
        border: 1px solid #584238;
        background: #0d0f0d;
        padding: 2.5rem;
        position: relative;
        transition: border-color 0.3s;
    }
    .testimonial-card:hover { border-color: rgba(255,182,147,0.4); }
    .testimonial-card.featured { border-color: #ea6b1e; background: #1a1c1a; }
    .quote-icon {
        font-family: 'Noto Serif', serif;
        font-size: 5rem; line-height: 1;
        color: rgba(234,107,30,0.2);
        position: absolute; top: 16px; right: 24px;
        pointer-events: none;
    }

    /* ── CTA section ── */
    .cta-section {
        background:
            linear-gradient(135deg, rgba(234,107,30,0.08) 0%, transparent 50%),
            #0d0f0d;
        border-top: 1px solid #584238;
        border-bottom: 1px solid #584238;
    }

    /* ── Ticker / marquee ── */
    .marquee-wrap { overflow: hidden; }
    .marquee-track {
        display: flex; gap: 48px; white-space: nowrap;
        animation: marquee 20s linear infinite;
    }
    .marquee-track:hover { animation-play-state: paused; }
    @keyframes marquee {
        from { transform: translateX(0); }
        to   { transform: translateX(-50%); }
    }
    .marquee-item {
        font-family: 'Noto Serif', serif;
        font-size: 0.8rem; font-weight: 600;
        letter-spacing: 0.2em; text-transform: uppercase;
        color: #584238;
        flex-shrink: 0;
    }
    .marquee-dot {
        color: #ea6b1e;
    }

    /* ── Stats ── */
    .stat-num {
        font-family: 'Noto Serif', serif;
        font-size: 3rem; font-weight: 700;
        color: #ffb693;
        line-height: 1;
    }
</style>
@endsection

@section('content')

{{-- ══ HERO ══ --}}
<section class="hero-section">
    <div class="hero-grid"></div>
    <div class="hero-orb" style="width:500px;height:500px;right:-100px;top:50%;margin-top:-250px;background:radial-gradient(circle,rgba(234,107,30,0.18),transparent 70%)"></div>
    <div class="hero-orb" style="width:300px;height:300px;left:20%;top:10%;background:radial-gradient(circle,rgba(233,195,73,0.08),transparent 70%);animation-delay:3s"></div>
    <div class="hero-bg"></div>

    <div class="relative z-10 max-w-[1200px] mx-auto px-6 w-full pt-28 pb-20">
        <div class="max-w-3xl">
            {{-- Pre-title --}}
            <div class="flex items-center gap-3 mb-8 reveal" style="transition-delay:0.1s">
                <div class="h-px w-12 bg-primary-container"></div>
                <span class="text-primary-container font-sans text-xs font-bold tracking-[0.25em] uppercase">
                    Fine Dining · Sài Gòn
                </span>
            </div>

            {{-- Headline --}}
            <h1 class="font-serif text-[clamp(3rem,8vw,6rem)] font-bold leading-[1.05] tracking-tight text-on-surface mb-8 reveal" style="transition-delay:0.2s">
                Nghệ thuật<br>
                <span style="color:#ffb693">ẩm thực</span><br>
                đỉnh cao
            </h1>

            <p class="text-on-surface-variant text-lg leading-relaxed max-w-xl mb-10 reveal" style="transition-delay:0.35s">
                PND Restaurant — nơi từng món ăn là một tác phẩm, từng khoảnh khắc là một kỷ niệm. 
                Trải nghiệm ẩm thực thượng lưu giữa lòng Sài Gòn.
            </p>

            <div class="flex flex-wrap gap-4 reveal" style="transition-delay:0.5s">
                <a href="{{ route('guest.menu') }}" class="btn-cta">
                    Xem thực đơn
                </a>
                <a href="{{ route('guest.contact') }}"
                   class="inline-flex items-center gap-2 border border-outline-variant text-on-surface-variant px-6 py-[10px] text-xs font-bold font-sans tracking-widest uppercase hover:border-primary hover:text-primary transition-all">
                    <span class="material-symbols-outlined text-sm">calendar_month</span>
                    Đặt bàn
                </a>
            </div>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-2 reveal" style="transition-delay:0.8s">
        <span class="text-outline text-xs tracking-widest uppercase font-sans">Khám phá</span>
        <div class="w-px h-10 bg-gradient-to-b from-outline to-transparent animate-pulse"></div>
    </div>
</section>

{{-- ══ MARQUEE ══ --}}
<div class="marquee-wrap py-5 border-y border-outline-variant overflow-hidden">
    <div class="marquee-track">
        @php
            $items = ['Món Chính', 'Khai Vị', 'Rượu Vang', 'Tráng Miệng', 'Đặt Bàn Riêng Tư', 'Bếp Trưởng 5 Sao', 'Nguyên Liệu Organic', 'Trải Nghiệm Thượng Hạng'];
        @endphp
        @foreach(array_merge($items, $items) as $item)
            <span class="marquee-item">{{ $item }}<span class="marquee-dot mx-6">✦</span></span>
        @endforeach
    </div>
</div>

{{-- ══ CATEGORIES ══ --}}
@if($categories->isNotEmpty())
<section class="py-20 px-6 max-w-[1200px] mx-auto">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 reveal">
        <div>
            <span class="text-primary-container font-sans text-xs font-bold tracking-[0.2em] uppercase block mb-3">
                — Danh mục
            </span>
            <h2 class="font-serif text-4xl font-bold text-on-surface">Khám phá thực đơn</h2>
        </div>
        <a href="{{ route('guest.menu') }}" class="text-primary font-sans text-sm font-semibold tracking-wider uppercase hover:text-secondary transition-colors flex items-center gap-2 shrink-0">
            Xem tất cả
            <span class="material-symbols-outlined text-base">arrow_forward</span>
        </a>
    </div>

    <div class="flex flex-wrap gap-3 reveal" style="transition-delay:0.15s">
        <a href="{{ route('guest.menu') }}" class="cat-pill">
            <span class="material-symbols-outlined text-sm" style="position:relative;z-index:1">restaurant_menu</span>
            <span>Tất cả</span>
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('guest.menu', ['category' => $cat->slug]) }}" class="cat-pill">
                <span>{{ $cat->name }}</span>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ══ FEATURED ITEMS ══ --}}
@if($featuredItems->isNotEmpty())
<section class="pb-24 px-6 max-w-[1200px] mx-auto">
    <div class="mb-10 reveal">
        <span class="text-primary-container font-sans text-xs font-bold tracking-[0.2em] uppercase block mb-3">— Món nổi bật</span>
        <h2 class="font-serif text-4xl font-bold text-on-surface">Tinh hoa bếp PND</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($featuredItems as $i => $item)
        <article class="menu-card reveal" style="transition-delay:{{ $i * 0.08 }}s">
            <div class="overflow-hidden relative">
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}"
                         alt="{{ $item->name }}"
                         class="menu-card-img">
                @else
                    <div class="menu-card-img flex items-center justify-center" style="background:#1f201e">
                        <span class="material-symbols-outlined text-4xl" style="color:#584238">restaurant</span>
                    </div>
                @endif
                @if($item->category)
                    <div class="menu-card-badge">{{ $item->category->name }}</div>
                @endif
            </div>
            <div class="p-5">
                <div class="flex justify-between items-baseline mb-2">
                    <h3 class="font-serif text-lg font-semibold text-on-surface">{{ $item->name }}</h3>
                    <span class="price-tag shrink-0 ml-3">
                        {{ number_format($item->price, 0, ',', '.') }}đ
                    </span>
                </div>
                <div class="h-px bg-outline-variant mb-3"></div>
                @if($item->description)
                    <p class="text-on-surface-variant text-sm leading-relaxed line-clamp-2">
                        {{ $item->description }}
                    </p>
                @endif
            </div>
        </article>
        @endforeach
    </div>

    <div class="text-center mt-12 reveal">
        <a href="{{ route('guest.menu') }}" class="btn-cta">
            Xem toàn bộ thực đơn
        </a>
    </div>
</section>
@endif

{{-- ══ STATS ══ --}}
<section class="py-20 border-y border-outline-variant" style="background:#0d0f0d">
    <div class="max-w-[1200px] mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            @php
                $stats = [
                    ['num' => '12+', 'label' => 'Năm kinh nghiệm'],
                    ['num' => '50+', 'label' => 'Món ăn độc quyền'],
                    ['num' => '98%', 'label' => 'Khách hàng hài lòng'],
                    ['num' => '3★', 'label' => 'Michelin Stars'],
                ];
            @endphp
            @foreach($stats as $i => $stat)
            <div class="reveal" style="transition-delay:{{ $i * 0.1 }}s">
                <div class="stat-num mb-2">{{ $stat['num'] }}</div>
                <div class="text-on-surface-variant text-xs font-sans font-semibold tracking-widest uppercase">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ TESTIMONIALS ══ --}}
<section class="py-24 px-6 max-w-[1200px] mx-auto">
    <div class="mb-12 reveal">
        <span class="text-primary-container font-sans text-xs font-bold tracking-[0.2em] uppercase block mb-3">— Khách hàng nói gì</span>
        <h2 class="font-serif text-4xl font-bold text-on-surface">Những đánh giá thượng lưu</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $reviews = [
                ['name' => 'Ông Nguyễn Hoàng Nam', 'title' => 'CEO tại Global Tech', 'text' => 'Một trải nghiệm ẩm thực không thể quên. Sự tỉ mỉ trong từng chi tiết từ món ăn đến cách phục vụ thực sự xứng đáng với đẳng cấp Michelin.', 'featured' => false],
                ['name' => 'Bà Elena Rodriguez', 'title' => 'Nhà Thiết Kế Nội Thất', 'text' => 'Không gian tối giản nhưng vô cùng quyền lực. Ánh sáng cam ấm áp tạo nên một sự tương phản hoàn hảo với nội thất đen huyền bí.', 'featured' => true],
                ['name' => 'Ông David Trần', 'title' => 'Nhà Sưu Tầm Vang', 'text' => 'Danh mục rượu vang thực sự gây ấn tượng mạnh. Tôi đã tìm thấy những chai vintage hiếm có mà hiếm nơi nào ở thành phố này sở hữu.', 'featured' => false],
            ];
        @endphp
        @foreach($reviews as $i => $review)
        <div class="testimonial-card {{ $review['featured'] ? 'featured' : '' }} reveal" style="transition-delay:{{ $i * 0.1 }}s">
            <div class="quote-icon">"</div>
            <p class="text-on-surface text-sm leading-relaxed mb-8 italic relative z-10">
                "{{ $review['text'] }}"
            </p>
            <div class="border-t border-outline-variant pt-5">
                <div class="font-serif text-base font-semibold text-primary">{{ $review['name'] }}</div>
                <div class="text-on-surface-variant text-xs uppercase tracking-wider mt-1">{{ $review['title'] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- ══ CTA FINAL ══ --}}
<section class="cta-section py-24 px-6 text-center">
    <div class="max-w-2xl mx-auto">
        <span class="text-primary-container font-sans text-xs font-bold tracking-[0.2em] uppercase block mb-4 reveal">— Bắt đầu hành trình</span>
        <h2 class="font-serif text-[clamp(2rem,5vw,3.5rem)] font-bold text-on-surface mb-6 reveal" style="transition-delay:0.1s">
            Đừng bỏ lỡ những đêm tiệc đặc biệt
        </h2>
        <p class="text-on-surface-variant leading-relaxed mb-10 reveal" style="transition-delay:0.2s">
            Đăng ký nhận thông tin về các thực đơn giới hạn và sự kiện độc quyền tại PND Restaurant.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto reveal" style="transition-delay:0.3s">
            <input type="email" placeholder="Email của bạn"
                   class="flex-grow bg-transparent border-b-2 border-outline-variant text-on-surface focus:outline-none focus:border-primary-container py-3 px-2 placeholder:text-outline font-sans text-sm transition-colors">
            <button class="btn-cta shrink-0">Đăng ký</button>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
    // Scroll reveal
    const revealEls = document.querySelectorAll('.reveal');
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); }
        });
    }, { threshold: 0.12 });
    revealEls.forEach(el => io.observe(el));

    // Trigger hero reveals immediately
    document.querySelectorAll('.hero-section .reveal').forEach((el, i) => {
        setTimeout(() => el.classList.add('visible'), 100 + i * 150);
    });
</script>
@endsection