@extends('layouts.guest')

@section('title', 'Đặt bàn — PND Restaurant')

@section('page-styles')
<style>
    .page-hero {
        padding: 140px 24px 48px;
        position: relative; overflow: hidden;
    }
    .page-hero::before {
        content: '';
        position: absolute; inset: 0;
        background: radial-gradient(ellipse at 80% 100%, rgba(234,107,30,0.1) 0%, transparent 55%);
    }

    /* Step cards */
    .step-card {
        background: #1f201e;
        border: 1px solid #584238;
        padding: 36px;
        position: relative; overflow: hidden;
    }
    .step-label {
        font-family: 'Work Sans', sans-serif;
        font-size: 0.65rem; font-weight: 700;
        letter-spacing: 0.2em; text-transform: uppercase;
        color: #ea6b1e; margin-bottom: 4px;
    }
    .step-title {
        font-family: 'Noto Serif', serif;
        font-size: 1.3rem; font-weight: 700;
        color: #e3e2e0; margin-bottom: 24px;
    }
    .step-deco {
        position: absolute; top: -20px; right: -20px;
        font-family: 'Noto Serif', serif;
        font-size: 6rem; font-weight: 700; line-height: 1;
        color: rgba(234,107,30,0.06); pointer-events: none; user-select: none;
    }

    /* Form inputs */
    .f-label {
        font-family: 'Work Sans', sans-serif;
        font-size: 0.68rem; font-weight: 700;
        letter-spacing: 0.15em; text-transform: uppercase;
        color: #a78b7e; display: block; margin-bottom: 8px;
    }
    .f-input {
        width: 100%; background: transparent;
        border: none; border-bottom: 1px solid #584238;
        color: #e3e2e0;
        font-family: 'Work Sans', sans-serif; font-size: 0.95rem;
        padding: 12px 0; outline: none;
        transition: border-color 0.3s;
        appearance: none; -webkit-appearance: none;
    }
    .f-input:focus { border-bottom-color: #ea6b1e; }
    .f-input::placeholder { color: #584238; }
    .f-error { color: #f87171; font-size: 0.7rem; margin-top: 4px; display: block; }

    /* Search button */
    .search-btn {
        display: inline-flex; align-items: center; gap: 8px;
        background: #ea6b1e; color: #4b1b00;
        font-family: 'Work Sans', sans-serif; font-weight: 700;
        font-size: 0.75rem; letter-spacing: 0.15em; text-transform: uppercase;
        padding: 14px 28px; border: none; cursor: pointer;
        transition: box-shadow 0.2s, transform 0.15s;
    }
    .search-btn:hover { box-shadow: 0 0 24px rgba(234,107,30,0.4); transform: translateY(-1px); }
    .search-btn:active { transform: scale(0.98); }

    /* Table radio cards */
    .table-radio { display: none; }
    .table-card {
        border: 1px solid #584238;
        background: #1a1c1a;
        padding: 18px; cursor: pointer;
        transition: border-color 0.25s, background 0.25s, transform 0.2s;
        position: relative;
    }
    .table-card:hover { border-color: rgba(255,182,147,0.5); transform: translateY(-2px); }
    .table-radio:checked + .table-card {
        border-color: #ea6b1e;
        background: rgba(234,107,30,0.07);
    }
    .table-radio:checked + .table-card .table-check {
        opacity: 1; transform: scale(1);
    }
    .table-check {
        position: absolute; top: 10px; right: 10px;
        width: 22px; height: 22px; border-radius: 50%;
        background: #ea6b1e;
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transform: scale(0.5);
        transition: opacity 0.2s, transform 0.2s;
    }
    .table-check .material-symbols-outlined { font-size: 0.85rem; color: #4b1b00; }

    /* Available badge */
    .avail-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-family: 'Work Sans', sans-serif;
        font-size: 0.6rem; font-weight: 700;
        letter-spacing: 0.1em; text-transform: uppercase;
        color: #4ade80; border: 1px solid rgba(74,222,128,0.35);
        background: rgba(74,222,128,0.07); padding: 3px 10px;
    }

    /* Time summary box */
    .time-summary {
        background: rgba(234,107,30,0.07);
        border: 1px solid rgba(234,107,30,0.3);
        padding: 16px 20px;
    }

    /* No table box */
    .no-table-box {
        border: 1px dashed rgba(234,107,30,0.4);
        background: rgba(234,107,30,0.04);
        padding: 48px 24px; text-align: center;
    }

    /* Submit btn */
    .submit-btn {
        width: 100%; background: #ea6b1e; color: #4b1b00;
        font-family: 'Work Sans', sans-serif; font-weight: 700;
        font-size: 0.8rem; letter-spacing: 0.18em; text-transform: uppercase;
        padding: 18px; border: none; cursor: pointer;
        transition: box-shadow 0.2s, transform 0.15s;
        position: relative; overflow: hidden;
    }
    .submit-btn::before {
        content: ''; position: absolute; inset: 0;
        background: rgba(255,255,255,0.12);
        transform: scaleX(0); transform-origin: left;
        transition: transform 0.35s;
    }
    .submit-btn:hover::before { transform: scaleX(1); }
    .submit-btn:hover { box-shadow: 0 0 36px rgba(234,107,30,0.45); }

    /* Guest info inputs — same style but on dark bg */
    .g-input {
        width: 100%; background: #1a1c1a;
        border: 1px solid #584238;
        color: #e3e2e0;
        font-family: 'Work Sans', sans-serif; font-size: 0.9rem;
        padding: 13px 16px; outline: none;
        transition: border-color 0.3s;
    }
    .g-input:focus { border-color: #ea6b1e; }

    /* Reveal */
    .reveal { opacity: 0; transform: translateY(22px); transition: opacity 0.55s ease, transform 0.55s ease; }
    .reveal.visible { opacity: 1; transform: none; }
</style>
@endsection

@section('content')

{{-- ── Hero ── --}}
<div class="page-hero mt-12">
    <div class="max-w-[800px] mx-auto relative z-10">
        <div class="flex items-center gap-3 mb-5 reveal">
            <a href="{{ route('guest.bookings.index') }}"
               class="flex items-center gap-1 text-on-surface-variant hover:text-primary text-xs font-sans font-semibold tracking-widest uppercase transition-colors">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Lịch của tôi
            </a>
        </div>
        <div class="flex items-center gap-3 mb-4 reveal" style="transition-delay:0.05s">
            <div class="h-px w-8 bg-primary-container"></div>
            <span class="text-primary-container font-sans text-xs font-bold tracking-[0.2em] uppercase">Đặt bàn</span>
        </div>
        <h1 class="font-serif text-[clamp(2.2rem,5vw,3.5rem)] font-bold text-on-surface leading-tight reveal" style="transition-delay:0.1s">
            Chọn thời gian<br>
            <span style="color:#ffb693">& bàn của bạn</span>
        </h1>
        <p class="text-on-surface-variant mt-4 max-w-md reveal" style="transition-delay:0.18s">
            Điền thông tin bên dưới — hệ thống sẽ tìm bàn phù hợp trong tích tắc.
        </p>
    </div>
</div>

<div class="max-w-[800px] mx-auto px-6 pb-24 space-y-6">

    {{-- ══ STEP 1 — Tìm bàn ══ --}}
    <div class="step-card reveal" style="transition-delay:0.2s">
        <div class="step-deco">01</div>
        <div class="step-label">Bước 1</div>
        <div class="step-title">Chọn thời gian & số người</div>

        <form method="GET" action="{{ route('guest.bookings.create') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">

                <div>
                    <label class="f-label">
                        Ngày <span style="color:#f87171">*</span>
                    </label>
                    <input type="date" name="date"
                           value="{{ request('date') }}"
                           min="{{ now()->format('Y-m-d') }}"
                           class="f-input">
                    @error('date')
                        <span class="f-error">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="f-label">
                        Giờ bắt đầu <span style="color:#f87171">*</span>
                    </label>
                    <input type="time" name="start_time"
                           value="{{ request('start_time') }}"
                           class="f-input">
                    @error('start_time')
                        <span class="f-error">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="f-label">
                        Số người <span style="color:#f87171">*</span>
                    </label>
                    <input type="number" name="guest_count"
                           min="1" max="20"
                           value="{{ request('guest_count') }}"
                           placeholder="VD: 4"
                           class="f-input">
                    @error('guest_count')
                        <span class="f-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button type="submit" class="search-btn">
                <span class="material-symbols-outlined text-base">search</span>
                Tìm bàn trống
            </button>
        </form>
    </div>

    {{-- ══ STEP 2 — Chọn bàn & xác nhận ══ --}}
    @if(request()->filled(['date', 'start_time', 'guest_count']))

        @if($tables->isEmpty())
            <div class="no-table-box reveal">
                <span class="material-symbols-outlined text-4xl block mb-3" style="color:#ea6b1e">event_busy</span>
                <p class="font-serif text-lg text-on-surface mb-1">Không có bàn trống</p>
                <p class="text-on-surface-variant text-sm">Vui lòng thử khung giờ hoặc ngày khác.</p>
            </div>

        @else
            <div class="step-card reveal" style="transition-delay:0.08s">
                <div class="step-deco">02</div>
                <div class="step-label">Bước 2</div>
                <div class="step-title">Chọn bàn & điền thông tin</div>

                <form action="{{ route('guest.bookings.store') }}" method="POST" class="space-y-8">
                    @csrf

                    {{-- Hidden fields --}}
                    <input type="hidden" name="start_time"
                           value="{{ request('date') . ' ' . request('start_time') }}">
                    <input type="hidden" name="end_time"
                           value="{{ \Carbon\Carbon::parse(request('date') . ' ' . request('start_time'))->addHours(3)->format('Y-m-d H:i:s') }}">
                    <input type="hidden" name="guest_count" value="{{ request('guest_count') }}">

                    {{-- Time summary --}}
                    <div class="time-summary flex flex-wrap items-center gap-x-6 gap-y-2">
                        <div class="flex items-center gap-2 text-sm text-primary">
                            <span class="material-symbols-outlined text-base text-primary-container">schedule</span>
                            <strong>
                                {{ \Carbon\Carbon::parse(request('date') . ' ' . request('start_time'))->format('H:i') }}
                            </strong>
                            <span style="color:#584238">→</span>
                            <strong>
                                {{ \Carbon\Carbon::parse(request('date') . ' ' . request('start_time'))->addHours(3)->format('H:i') }}
                            </strong>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-primary">
                            <span class="material-symbols-outlined text-base text-primary-container">calendar_month</span>
                            {{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}
                        </div>
                        <div class="flex items-center gap-2 text-sm text-on-surface-variant text-xs ml-auto">
                            <span class="material-symbols-outlined text-sm" style="color:#a78b7e">info</span>
                            Tối đa 3 tiếng — bàn tự động trả về sau thời gian này
                        </div>
                    </div>

                    {{-- Table grid --}}
                    <div>
                        <label class="f-label mb-4 block">
                            Chọn bàn <span style="color:#f87171">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($tables as $table)
                                <label>
                                    <input type="radio" name="table_id"
                                           value="{{ $table->id }}"
                                           class="table-radio"
                                           {{ old('table_id') == $table->id ? 'checked' : '' }}>
                                    <div class="table-card">
                                        <div class="table-check">
                                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">check</span>
                                        </div>
                                        <div class="flex items-start justify-between mb-3">
                                            <span class="font-serif font-semibold text-on-surface">{{ $table->name }}</span>
                                            <span class="avail-badge">
                                                <span class="material-symbols-outlined" style="font-size:0.65rem;font-variation-settings:'FILL' 1">circle</span>
                                                Trống
                                            </span>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2 text-xs text-on-surface-variant">
                                                <span class="material-symbols-outlined text-sm" style="color:#ea6b1e">location_on</span>
                                                {{ $table->area->name }}
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-on-surface-variant">
                                                <span class="material-symbols-outlined text-sm" style="color:#ea6b1e">group</span>
                                                Tối đa {{ $table->capacity }} người
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('table_id')
                            <span class="f-error mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Guest info --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="f-label">Họ tên <span style="color:#f87171">*</span></label>
                            <input type="text" name="guest_name"
                                   value="{{ old('guest_name', auth()->user()->name) }}"
                                   class="g-input">
                            @error('guest_name')
                                <span class="f-error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="f-label">Số điện thoại <span style="color:#f87171">*</span></label>
                            <input type="text" name="guest_phone"
                                   value="{{ old('guest_phone', auth()->user()->phone ?? '') }}"
                                   class="g-input">
                            @error('guest_phone')
                                <span class="f-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Note --}}
                    <div>
                        <label class="f-label">Ghi chú</label>
                        <textarea name="note" rows="3"
                                  placeholder="VD: Sinh nhật, dị ứng hải sản, yêu cầu đặc biệt..."
                                  class="g-input" style="resize:none">{{ old('note') }}</textarea>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="submit-btn">
                        <span class="material-symbols-outlined align-middle mr-2">check_circle</span>
                        Xác nhận đặt bàn
                    </button>
                </form>
            </div>
        @endif
    @endif

</div>

@endsection

@section('scripts')
<script>
    const els = document.querySelectorAll('.reveal');
    const io = new IntersectionObserver(e => e.forEach(x => { if(x.isIntersecting){ x.target.classList.add('visible'); io.unobserve(x.target); }}), {threshold:0.08});
    els.forEach(el => io.observe(el));
    document.querySelectorAll('.page-hero .reveal').forEach((el,i) => setTimeout(() => el.classList.add('visible'), 60+i*120));
</script>
@endsection