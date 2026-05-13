@extends('layouts.guest')

@section('title', 'Liên hệ — PND Restaurant')

@section('page-styles')
<style>
    /* ── Hero ── */
    .contact-hero {
        padding: 160px 24px 60px;
        position: relative; overflow: hidden;
    }
    .contact-hero::before {
        content: '';
        position: absolute; inset: 0;
        background: radial-gradient(ellipse at 30% 100%, rgba(234,107,30,0.1) 0%, transparent 55%);
    }

    /* ── Reveal ── */
    .reveal {
        opacity: 0; transform: translateY(24px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .reveal.visible { opacity: 1; transform: none; }

    /* ── Info card ── */
    .info-card {
        display: flex; align-items: flex-start; gap: 16px;
        padding: 20px; border: 1px solid #584238;
        background: #1f201e;
        transition: border-color 0.3s;
    }
    .info-card:hover { border-color: rgba(255,182,147,0.5); }
    .info-icon {
        width: 44px; height: 44px; flex-shrink: 0;
        border: 1px solid #ea6b1e;
        display: flex; align-items: center; justify-content: center;
        color: #ea6b1e;
    }

    /* ── Form ── */
    .form-field {
        display: flex; flex-direction: column; gap: 8px;
    }
    .form-label {
        font-family: 'Work Sans', sans-serif;
        font-size: 0.7rem; font-weight: 700;
        letter-spacing: 0.15em; text-transform: uppercase;
        color: #a78b7e;
    }
    .form-input {
        background: transparent;
        border: none;
        border-bottom: 1px solid #584238;
        color: #e3e2e0;
        font-family: 'Work Sans', sans-serif;
        font-size: 0.95rem;
        padding: 12px 0;
        outline: none;
        transition: border-color 0.3s;
        width: 100%;
    }
    .form-input:focus { border-bottom-color: #ea6b1e; }
    .form-input::placeholder { color: #584238; }
    .form-input option { background: #1f201e; color: #e3e2e0; }

    /* ── Map placeholder ── */
    .map-box {
        width: 100%; aspect-ratio: 1;
        background: #1f201e;
        border: 1px solid #584238;
        overflow: hidden; position: relative;
        transition: border-color 0.3s;
    }
    .map-box:hover { border-color: rgba(255,182,147,0.4); }
    .map-img {
        width: 100%; height: 100%; object-fit: cover;
        filter: grayscale(80%) brightness(0.7);
        transition: filter 0.5s;
    }
    .map-box:hover .map-img { filter: grayscale(20%) brightness(0.9); }
    .map-pin {
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -100%);
        color: #ea6b1e;
        animation: pinBounce 2s ease-in-out infinite;
    }
    @keyframes pinBounce {
        0%,100% { transform: translate(-50%,-100%); }
        50%      { transform: translate(-50%,-120%); }
    }

    /* ── Hours table ── */
    .hours-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(88,66,56,0.4); }
    .hours-row:last-child { border-bottom: none; }
    .hours-row.today { background: rgba(234,107,30,0.06); padding: 10px 8px; margin: 0 -8px; }

    /* ── Submit btn ── */
    .submit-btn {
        background: #ea6b1e; color: #4b1b00;
        font-family: 'Work Sans', sans-serif;
        font-weight: 700; font-size: 0.75rem;
        letter-spacing: 0.18em; text-transform: uppercase;
        padding: 16px 40px; border: none; cursor: pointer;
        transition: all 0.2s;
        position: relative; overflow: hidden;
    }
    .submit-btn::before {
        content: '';
        position: absolute; inset: 0;
        background: rgba(255,255,255,0.15);
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.3s;
    }
    .submit-btn:hover::before { transform: scaleX(1); }
    .submit-btn:hover { box-shadow: 0 0 30px rgba(234,107,30,0.5); }
    .submit-btn:active { transform: scale(0.98); }

    /* ── Decorative corner ── */
    .deco-corner-tr {
        position: absolute; top: -2px; right: -2px;
        width: 60px; height: 60px;
        border-top: 2px solid rgba(234,107,30,0.5);
        border-right: 2px solid rgba(234,107,30,0.5);
        pointer-events: none;
    }
    .deco-corner-bl {
        position: absolute; bottom: -2px; left: -2px;
        width: 60px; height: 60px;
        border-bottom: 2px solid rgba(234,107,30,0.3);
        border-left: 2px solid rgba(234,107,30,0.3);
        pointer-events: none;
    }
</style>
@endsection

@section('content')

{{-- ── Hero ── --}}
<div class="contact-hero mt-12">
    <div class="max-w-[1200px] mx-auto relative z-10">
        <div class="flex items-center gap-3 mb-6 reveal">
            <div class="h-px w-10 bg-primary-container"></div>
            <span class="text-primary-container font-sans text-xs font-bold tracking-[0.2em] uppercase">Liên hệ</span>
        </div>
        <h1 class="font-serif text-[clamp(3rem,7vw,5rem)] font-bold leading-[1.05] tracking-tight text-on-surface mb-4 reveal" style="transition-delay:0.1s">
            Nói chuyện<br><span style="color:#ffb693">với chúng tôi</span>
        </h1>
        <p class="text-on-surface-variant max-w-lg leading-relaxed reveal" style="transition-delay:0.2s">
            Dù là đặt bàn, tổ chức sự kiện riêng tư hay chỉ đơn giản là muốn chia sẻ — 
            chúng tôi luôn ở đây lắng nghe.
        </p>
    </div>
</div>

{{-- ── Main Grid ── --}}
<div class="max-w-[1200px] mx-auto px-6 pb-24">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">

        {{-- ── Left: Info + Map ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Contact info --}}
            @foreach([
                ['icon' => 'location_on', 'title' => 'Địa chỉ',  'lines' => ['123 Đường Lê Lợi, Quận 1', 'TP. Hồ Chí Minh']],
                ['icon' => 'call',        'title' => 'Điện thoại','lines' => ['+84 28 3456 7890', 'Phục vụ 24/7 cho đặt bàn']],
                ['icon' => 'mail',        'title' => 'Email',      'lines' => ['concierge@pndrestaurant.com', 'events@pndrestaurant.com']],
            ] as $i => $info)
            <div class="info-card reveal" style="transition-delay:{{ $i * 0.08 }}s">
                <div class="info-icon">
                    <span class="material-symbols-outlined">{{ $info['icon'] }}</span>
                </div>
                <div>
                    <div class="font-sans font-bold text-xs tracking-widest uppercase text-primary mb-2">{{ $info['title'] }}</div>
                    @foreach($info['lines'] as $line)
                        <div class="text-on-surface-variant text-sm">{{ $line }}</div>
                    @endforeach
                </div>
            </div>
            @endforeach

            {{-- Hours --}}
            <div class="border border-outline-variant p-5 reveal" style="transition-delay:0.3s;background:#1f201e">
                <div class="font-sans font-bold text-xs tracking-widest uppercase text-primary mb-4">Giờ mở cửa</div>
                @php
                    $today = now()->dayOfWeek; // 0=Sun, 1=Mon...
                    $days = [
                        ['label' => 'Thứ 2 — Thứ 6', 'hours' => '18:00 — 23:00', 'days' => [1,2,3,4,5]],
                        ['label' => 'Thứ 7',           'hours' => '17:30 — 23:30', 'days' => [6]],
                        ['label' => 'Chủ nhật',        'hours' => '17:30 — 22:30', 'days' => [0]],
                    ];
                @endphp
                @foreach($days as $day)
                <div class="hours-row {{ in_array($today, $day['days']) ? 'today' : '' }}">
                    <span class="text-sm {{ in_array($today, $day['days']) ? 'text-primary font-semibold' : 'text-on-surface-variant' }}">
                        {{ $day['label'] }}
                        @if(in_array($today, $day['days']))
                            <span class="text-xs text-primary-container ml-1">• Hôm nay</span>
                        @endif
                    </span>
                    <span class="text-sm text-on-surface font-sans font-medium">{{ $day['hours'] }}</span>
                </div>
                @endforeach

                <div class="mt-4 pt-4 border-t border-outline-variant">
                    <div class="font-sans text-xs text-on-surface-variant">
                        <span class="material-symbols-outlined text-sm align-middle mr-1" style="color:#ea6b1e">checkroom</span>
                        Dress code: Smart Casual / Formal
                    </div>
                </div>
            </div>

            {{-- Map --}}
            <div class="map-box reveal" style="transition-delay:0.4s">
                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBKLT0-ppmEvezwfuzX6X51n2rTdzFTgvsGNKcNVeEkuw-sctJJ63ZQSVaKW6G5pFwBhgUSgUd_dGmHRZ8M7o0QN5DdiHU8vNiJlmFDi6yS404jsoIvxraT5VLCaCz9MO8EJCqmzEkEv3lIOJDRAkwf3LewTF1KXGv7CXkEi1cIntj7GFqp9hAO0OUPr1j9yx-LJRcw2iwPW8E_HaAv1s4CxcyuBBpdTpo-tSMS9MrJ1myp2Ck5mODhtun9WRYlXl9GqFKNU8g6pCcP"
                     alt="Bản đồ" class="map-img">
                <div class="map-pin">
                    <span class="material-symbols-outlined text-4xl" style="font-variation-settings:'FILL' 1">location_on</span>
                </div>
                <div style="position:absolute;bottom:12px;left:12px;background:rgba(18,20,18,0.85);padding:6px 14px;border:1px solid #584238">
                    <span class="font-sans text-xs text-primary font-semibold">PND Restaurant</span>
                </div>
            </div>
        </div>

        {{-- ── Right: Contact Form ── --}}
        <div class="lg:col-span-3">
            <div class="border border-outline-variant p-8 md:p-12 relative reveal" style="background:#1a1c1a;transition-delay:0.1s">
                <div class="deco-corner-tr"></div>
                <div class="deco-corner-bl"></div>

                <h2 class="font-serif text-3xl font-bold text-on-surface mb-8">Gửi tin nhắn</h2>

                @if(session('contact_success'))
                    <div class="mb-6 border border-primary-container p-4"
                         style="background:rgba(234,107,30,0.1)">
                        <p class="text-primary text-sm font-sans flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">check_circle</span>
                            Cảm ơn bạn! Chúng tôi sẽ phản hồi trong vòng 24 giờ.
                        </p>
                    </div>
                @endif

                <form action="{{ route('guest.contact.send') }}" method="POST" class="space-y-8">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="form-field">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" name="name" class="form-input"
                                   placeholder="Nguyễn Văn A"
                                   value="{{ old('name') }}">
                            @error('name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-field">
                            <label class="form-label">Địa chỉ Email</label>
                            <input type="email" name="email" class="form-input"
                                   placeholder="email@example.com"
                                   value="{{ old('email') }}">
                            @error('email') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="form-field">
                            <label class="form-label">Số điện thoại</label>
                            <input type="tel" name="phone" class="form-input"
                                   placeholder="0901 234 567"
                                   value="{{ old('phone') }}">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Số khách</label>
                            <input type="number" name="guests" class="form-input"
                                   placeholder="2" min="1" max="50"
                                   value="{{ old('guests') }}">
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="form-label">Chủ đề</label>
                        <select name="subject" class="form-input" style="cursor:pointer;appearance:none">
                            <option value="reservation" {{ old('subject') == 'reservation' ? 'selected' : '' }}>Đặt bàn riêng tư</option>
                            <option value="event"       {{ old('subject') == 'event'       ? 'selected' : '' }}>Sự kiện & Hội nghị</option>
                            <option value="feedback"    {{ old('subject') == 'feedback'    ? 'selected' : '' }}>Góp ý dịch vụ</option>
                            <option value="other"       {{ old('subject') == 'other'       ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>

                    <div class="form-field">
                        <label class="form-label">Tin nhắn của bạn</label>
                        <textarea name="message" class="form-input" rows="5"
                                  placeholder="Hãy cho chúng tôi biết yêu cầu của bạn..."
                                  style="resize:none">{{ old('message') }}</textarea>
                        @error('message') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="submit-btn w-full md:w-auto">
                            Gửi yêu cầu
                        </button>
                    </div>
                </form>

                {{-- Social links --}}
                <div class="mt-12 pt-8 border-t border-outline-variant flex items-center gap-6">
                    <span class="text-on-surface-variant text-xs font-sans tracking-widest uppercase">Theo dõi</span>
                    <a href="#" class="flex items-center gap-2 text-on-surface-variant hover:text-primary text-sm transition-colors">
                        <span class="material-symbols-outlined text-base">photo_camera</span> Instagram
                    </a>
                    <a href="#" class="flex items-center gap-2 text-on-surface-variant hover:text-primary text-sm transition-colors">
                        <span class="material-symbols-outlined text-base">social_leaderboard</span> Facebook
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
    const revealEls = document.querySelectorAll('.reveal');
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); } });
    }, { threshold: 0.08 });
    revealEls.forEach(el => io.observe(el));
    document.querySelectorAll('.contact-hero .reveal').forEach((el,i) => {
        setTimeout(() => el.classList.add('visible'), 80 + i*140);
    });
</script>
@endsection