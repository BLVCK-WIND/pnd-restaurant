@extends('layouts.guest')

@section('title', 'Về chúng tôi — PND Restaurant')

@section('page-styles')
<style>
    /* ── Hero ── */
    .about-hero {
        min-height: 70vh;
        display: flex; align-items: center;
        position: relative; overflow: hidden;
        padding: 160px 24px 80px;
    }
    .about-hero::before {
        content: '';
        position: absolute; inset: 0;
        background:
            linear-gradient(135deg, rgba(18,20,18,0.95) 40%, rgba(18,20,18,0.6) 100%),
            radial-gradient(ellipse at 80% 50%, rgba(234,107,30,0.15) 0%, transparent 60%);
    }
    .grid-overlay {
        position: absolute; inset: 0;
        background-image:
            linear-gradient(rgba(88,66,56,0.1) 1px, transparent 1px),
            linear-gradient(90deg, rgba(88,66,56,0.1) 1px, transparent 1px);
        background-size: 80px 80px;
    }

    /* ── Reveal ── */
    .reveal {
        opacity: 0; transform: translateY(28px);
        transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .reveal.visible { opacity: 1; transform: none; }
    .reveal-left {
        opacity: 0; transform: translateX(-32px);
        transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .reveal-left.visible { opacity: 1; transform: none; }
    .reveal-right {
        opacity: 0; transform: translateX(32px);
        transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .reveal-right.visible { opacity: 1; transform: none; }

    /* ── Story section ── */
    .story-image {
        width: 100%; height: 500px; object-fit: cover;
        border: 2px solid #584238;
        display: block;
        background: #1f201e;
        transition: border-color 0.3s;
    }
    .story-image:hover { border-color: #ffb693; }

    .story-year {
        font-family: 'Noto Serif', serif;
        font-size: 8rem; font-weight: 700; line-height: 1;
        color: rgba(255,182,147,0.08);
        position: absolute; bottom: -20px; right: -10px;
        pointer-events: none; user-select: none;
        letter-spacing: -0.04em;
    }

    /* ── Values ── */
    .value-card {
        border: 1px solid #584238;
        background: #0d0f0d;
        padding: 2rem;
        transition: border-color 0.3s, transform 0.3s;
        position: relative; overflow: hidden;
    }
    .value-card::before {
        content: '';
        position: absolute; top: 0; left: 0;
        width: 3px; height: 0;
        background: var(--primary-container);
        transition: height 0.4s ease;
    }
    .value-card:hover { border-color: rgba(255,182,147,0.4); transform: translateY(-3px); }
    .value-card:hover::before { height: 100%; }

    /* ── Team ── */
    .team-card {
        position: relative; overflow: hidden;
        border: 1px solid #584238;
        transition: border-color 0.3s;
    }
    .team-card:hover { border-color: #ffb693; }
    .team-img {
        width: 100%; aspect-ratio: 3/4; object-fit: cover;
        filter: grayscale(30%); transition: filter 0.4s, transform 0.5s;
        display: block; background: #292a29;
    }
    .team-card:hover .team-img { filter: grayscale(0%); transform: scale(1.04); }
    .team-info {
        padding: 20px;
        background: linear-gradient(to top, #0d0f0d 0%, #1f201e 100%);
        border-top: 1px solid #584238;
    }

    /* ── Sourcing ── */
    .sourcing-step {
        display: flex; gap: 20px; align-items: flex-start;
        padding: 24px; border: 1px solid #584238;
        background: #1f201e;
        transition: border-color 0.3s, background 0.3s;
    }
    .sourcing-step:hover { border-color: rgba(234,107,30,0.5); background: #292a29; }
    .step-icon {
        width: 48px; height: 48px; flex-shrink: 0;
        border: 1px solid #ea6b1e;
        display: flex; align-items: center; justify-content: center;
        color: #ea6b1e;
    }

    /* ── Timeline ── */
    .timeline-item { position: relative; padding-left: 40px; }
    .timeline-item::before {
        content: '';
        position: absolute; left: 8px; top: 24px; bottom: -32px;
        width: 1px; background: #584238;
    }
    .timeline-item:last-child::before { display: none; }
    .timeline-dot {
        position: absolute; left: 0; top: 18px;
        width: 17px; height: 17px; border-radius: 50%;
        border: 2px solid #ea6b1e;
        background: #121412;
        display: flex; align-items: center; justify-content: center;
    }
    .timeline-dot::after {
        content: ''; width: 7px; height: 7px;
        border-radius: 50%; background: #ea6b1e;
    }

    /* ── Big quote ── */
    .big-quote {
        font-family: 'Noto Serif', serif;
        font-size: clamp(1.5rem, 4vw, 2.5rem);
        font-weight: 600; font-style: italic;
        line-height: 1.4; color: #e3e2e0;
    }
</style>
@endsection

@section('content')

{{-- ── Hero ── --}}
<div class="about-hero mt-12">
    <div class="grid-overlay"></div>
    <div class="absolute inset-0 pointer-events-none"
         style="background:radial-gradient(ellipse at 70% 60%,rgba(234,107,30,0.12) 0%,transparent 55%)"></div>
    <div class="max-w-[1200px] mx-auto relative z-10 w-full">
        <div class="flex items-center gap-3 mb-6 reveal">
            <div class="h-px w-10 bg-primary-container"></div>
            <span class="text-primary-container font-sans text-xs font-bold tracking-[0.2em] uppercase">Về chúng tôi</span>
        </div>
        <h1 class="font-serif text-[clamp(3rem,8vw,5.5rem)] font-bold leading-[1.05] tracking-tight text-on-surface mb-6 reveal" style="transition-delay:0.1s">
            Câu chuyện<br>
            <span style="color:#ffb693">của PND</span>
        </h1>
        <p class="text-on-surface-variant max-w-lg leading-relaxed text-lg reveal" style="transition-delay:0.2s">
            Hành trình 12 năm kiến tạo những trải nghiệm ẩm thực vượt ra ngoài giới hạn của ngôn ngữ.
        </p>
    </div>
</div>

{{-- ── Our Story ── --}}
<section class="py-24 px-6 max-w-[1200px] mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
        <div class="relative reveal-left">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBKLT0-ppmEvezwfuzX6X51n2rTdzFTgvsGNKcNVeEkuw-sctJJ63ZQSVaKW6G5pFwBhgUSgUd_dGmHRZ8M7o0QN5DdiHU8vNiJlmFDi6yS404jsoIvxraT5VLCaCz9MO8EJCqmzEkEv3lIOJDRAkwf3LewTF1KXGv7CXkEi1cIntj7GFqp9hAO0OUPr1j9yx-LJRcw2iwPW8E_HaAv1s4CxcyuBBpdTpo-tSMS9MrJ1myp2Ck5mODhtun9WRYlXl9GqFKNU8g6pCcP"
                 alt="Bếp PND"
                 class="story-image">
            <div class="story-year">2012</div>
        </div>

        <div class="reveal-right">
            <span class="text-primary-container font-sans text-xs font-bold tracking-[0.2em] uppercase block mb-4">— Nguồn gốc</span>
            <h2 class="font-serif text-4xl font-bold text-on-surface mb-6">
                Từ một đam mê,<br>thành một đế chế
            </h2>
            <p class="text-on-surface-variant leading-relaxed mb-6">
                PND Restaurant được thành lập năm 2012 bởi bếp trưởng Phạm Ngọc Duy — người đã dành 
                8 năm tu nghiệp tại các nhà hàng Michelin ở Paris và Tokyo. Ông mang về Sài Gòn không 
                chỉ kỹ thuật, mà cả triết lý: ẩm thực là ngôn ngữ cảm xúc.
            </p>
            <p class="text-on-surface-variant leading-relaxed mb-10">
                Hôm nay, PND là điểm đến của những tâm hồn yêu cái đẹp — nơi ánh đèn mờ, hương thơm 
                và vị ngon hòa quyện thành một trải nghiệm không thể quên.
            </p>

            {{-- Timeline --}}
            <div class="space-y-8">
                @foreach([
                    ['year' => '2012', 'desc' => 'Khai trương tại Quận 1 với 20 bàn'],
                    ['year' => '2016', 'desc' => 'Nhận sao Michelin đầu tiên'],
                    ['year' => '2020', 'desc' => 'Mở rộng thực đơn Wine Pairing'],
                    ['year' => '2024', 'desc' => 'Top 50 nhà hàng tốt nhất Đông Nam Á'],
                ] as $milestone)
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div>
                        <span class="text-primary-container font-sans text-xs font-bold tracking-widest">{{ $milestone['year'] }}</span>
                        <p class="text-on-surface text-sm mt-1">{{ $milestone['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ── Big Quote ── --}}
<section class="py-20 border-y border-outline-variant" style="background:#0d0f0d">
    <div class="max-w-[900px] mx-auto px-6 text-center reveal">
        <div style="font-size:4rem;color:rgba(234,107,30,0.3);font-family:'Noto Serif',serif;line-height:1">"</div>
        <p class="big-quote mb-8">
            Chúng tôi không nấu ăn. Chúng tôi kể câu chuyện — bằng hương vị, bằng kết cấu, 
            bằng khoảnh khắc bạn cất chiếc nĩa xuống và nhắm mắt lại.
        </p>
        <div class="h-px w-16 bg-primary-container mx-auto mb-5"></div>
        <p class="text-primary font-serif font-semibold">Phạm Ngọc Duy</p>
        <p class="text-on-surface-variant text-xs font-sans tracking-widest uppercase mt-1">Founder & Executive Chef</p>
    </div>
</section>

{{-- ── Values ── --}}
<section class="py-24 px-6 max-w-[1200px] mx-auto">
    <div class="mb-14 reveal">
        <span class="text-primary-container font-sans text-xs font-bold tracking-[0.2em] uppercase block mb-3">— Triết lý</span>
        <h2 class="font-serif text-4xl font-bold text-on-surface">Những gì chúng tôi tin</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @php $values = [
            ['icon' => 'eco',        'title' => 'Tôn trọng nguyên liệu',    'desc' => 'Mỗi nguyên liệu được chọn lọc tỉ mỉ từ những trang trại hữu cơ được chứng nhận, thu hoạch đúng thời điểm để đảm bảo hương vị tinh túy nhất.'],
            ['icon' => 'palette',    'title' => 'Sáng tạo không ngừng',     'desc' => 'Thực đơn PND thay đổi theo mùa — không phải vì phong trào, mà vì thiên nhiên luôn ban tặng những điều bất ngờ xứng đáng được tôn vinh.'],
            ['icon' => 'handshake',  'title' => 'Phục vụ từ trái tim',      'desc' => 'Đội ngũ phục vụ được đào tạo không chỉ để phục vụ, mà để đọc hiểu cảm xúc và nhu cầu của từng vị khách trước khi họ nói ra.'],
            ['icon' => 'recycling',  'title' => 'Bền vững là trách nhiệm',  'desc' => 'Từ bao bì compostable đến chương trình zero-waste trong bếp, PND cam kết để lại dấu ấn trên đĩa ăn, không phải trên môi trường.'],
            ['icon' => 'school',     'title' => 'Học hỏi liên tục',          'desc' => 'Đội ngũ bếp thường xuyên tu nghiệp tại châu Âu và Nhật Bản, mang về những kỹ thuật mới nhất để làm phong phú thêm ngôn ngữ ẩm thực PND.'],
            ['icon' => 'favorite',   'title' => 'Cảm xúc là tiêu chuẩn',   'desc' => 'Chúng tôi chỉ gửi ra một món khi cả đội đồng lòng rằng nó tạo ra cảm giác gì đó — không phải chỉ no, mà là xúc động.'],
        ]; @endphp
        @foreach($values as $i => $v)
        <div class="value-card reveal" style="transition-delay:{{ $i * 0.08 }}s">
            <div class="step-icon mb-5" style="border-color:#584238">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">{{ $v['icon'] }}</span>
            </div>
            <h3 class="font-serif text-lg font-semibold text-on-surface mb-3">{{ $v['title'] }}</h3>
            <p class="text-on-surface-variant text-sm leading-relaxed">{{ $v['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ── Sourcing ── --}}
<section class="py-24 border-y border-outline-variant" style="background:#0d0f0d">
    <div class="max-w-[1200px] mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
            <div>
                <span class="text-primary-container font-sans text-xs font-bold tracking-[0.2em] uppercase block mb-4 reveal">— Quy trình chọn nguyên liệu</span>
                <h2 class="font-serif text-4xl font-bold text-on-surface mb-10 reveal" style="transition-delay:0.1s">
                    Khắt khe từ nguồn gốc
                </h2>
                <div class="space-y-4">
                    @foreach([
                        ['icon' => 'eco',      'title' => 'Canh tác hữu cơ 100%',   'desc' => 'Hợp tác trực tiếp với các trang trại biệt lập, thổ nhưỡng nuôi dưỡng tự nhiên không hóa chất.'],
                        ['icon' => 'verified', 'title' => 'Kiểm định nghiêm ngặt',  'desc' => 'Mỗi lô nguyên liệu trải qua 12 bước kiểm tra chất lượng từ độ tươi, hương vị đến dinh dưỡng.'],
                        ['icon' => 'schedule', 'title' => 'Thu hoạch theo giờ',     'desc' => 'Nguyên liệu thu hoạch lúc rạng sáng, vận chuyển đến bếp trong vòng 4 giờ để giữ trọn hương vị.'],
                    ] as $i => $step)
                    <div class="sourcing-step reveal" style="transition-delay:{{ 0.2 + $i * 0.1 }}s">
                        <div class="step-icon">
                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">{{ $step['icon'] }}</span>
                        </div>
                        <div>
                            <h4 class="font-sans font-semibold text-secondary text-sm tracking-wide mb-1">{{ $step['title'] }}</h4>
                            <p class="text-on-surface-variant text-sm leading-relaxed">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="reveal-right">
                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuByaE8PDd0l65n2HFpUjGWac_wSDKSbiFq0vh7P8lzb0_0SuGZ6o3b75z7J6-fW_Br3vsqvqkE_HFgmP9jUtLtc6A5PvvGLg4beNQ6V7Qlkh7-1xQsK1EYcXG7hwX3Z0sreRkupAyK2-uSVrgs0X3-lFS4A8HZVERSixCeRQav-NjYWYzPvtphtuO_PTgdmX-atNrMj_QavF4wBCGyqOWBSdRF7QIsw6lsUKr0GPYKaGvNehz4B55LYHB8OvpYw_t2QdHvxUbJk6pcl"
                     alt="Nguyên liệu hữu cơ"
                     class="w-full object-cover border-2 border-outline-variant hover:border-primary transition-colors"
                     style="height:500px">
            </div>
        </div>
    </div>
</section>

{{-- ── Team ── --}}
<section class="py-24 px-6 max-w-[1200px] mx-auto">
    <div class="mb-14 reveal">
        <span class="text-primary-container font-sans text-xs font-bold tracking-[0.2em] uppercase block mb-3">— Con người</span>
        <h2 class="font-serif text-4xl font-bold text-on-surface">Những nghệ nhân ẩm thực</h2>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @foreach([
            ['name' => 'Phạm Ngọc Duy',    'role' => 'Executive Chef & Founder',  'exp' => '20 năm kinh nghiệm'],
            ['name' => 'Trần Minh Khoa',    'role' => 'Sous Chef',                 'exp' => 'Đào tạo tại Tokyo'],
            ['name' => 'Lê Thanh Hương',    'role' => 'Pastry Chef',               'exp' => 'École de Pâtisserie Paris'],
            ['name' => 'Nguyễn Anh Tuấn',   'role' => 'Wine Sommelier',            'exp' => 'WSET Level 4'],
        ] as $i => $member)
        <div class="team-card reveal" style="transition-delay:{{ $i * 0.1 }}s">
            <div style="overflow:hidden">
                <div class="team-img" style="background:linear-gradient(135deg,#1f201e,#292a29);display:flex;align-items:center;justify-content:center;aspect-ratio:3/4">
                    <span class="material-symbols-outlined" style="font-size:3rem;color:#584238">person</span>
                </div>
            </div>
            <div class="team-info">
                <div class="font-serif font-semibold text-on-surface mb-1">{{ $member['name'] }}</div>
                <div class="text-primary text-xs font-sans font-semibold tracking-wide mb-1">{{ $member['role'] }}</div>
                <div class="text-on-surface-variant text-xs">{{ $member['exp'] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- ── CTA ── --}}
<section class="py-24 px-6 border-t border-outline-variant"
         style="background:radial-gradient(ellipse at center bottom,rgba(234,107,30,0.08) 0%,transparent 60%),#0d0f0d">
    <div class="max-w-xl mx-auto text-center reveal">
        <h2 class="font-serif text-4xl font-bold text-on-surface mb-6">Sẵn sàng cho trải nghiệm đỉnh cao?</h2>
        <p class="text-on-surface-variant leading-relaxed mb-10">
            Hãy để chúng tôi dẫn dắt bạn vào thế giới của những cung bậc cảm xúc ẩm thực chưa từng có.
        </p>
        <a href="{{ route('guest.bookings.index') }}" class="btn-cta">Đặt bàn ngay</a>
    </div>
</section>

@endsection

@section('scripts')
<script>
    const all = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); } });
    }, { threshold: 0.1 });
    all.forEach(el => io.observe(el));
    document.querySelectorAll('.about-hero .reveal').forEach((el,i) => {
        setTimeout(() => el.classList.add('visible'), 100 + i*160);
    });
</script>
@endsection