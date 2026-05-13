@extends('layouts.guest')

@section('title', 'Lịch đặt bàn — PND Restaurant')

@section('page-styles')
<style>
    .page-hero {
        padding: 140px 24px 48px;
        position: relative; overflow: hidden;
    }
    .page-hero::before {
        content: '';
        position: absolute; inset: 0;
        background: radial-gradient(ellipse at 20% 100%, rgba(234,107,30,0.1) 0%, transparent 55%);
    }

    /* Cards */
    .booking-card {
        background: #1f201e;
        border: 1px solid #584238;
        transition: border-color 0.3s, transform 0.3s;
        position: relative; overflow: hidden;
    }
    .booking-card::before {
        content: '';
        position: absolute; top: 0; left: 0;
        width: 3px; height: 0;
        transition: height 0.4s ease;
    }
    .booking-card:hover { border-color: rgba(255,182,147,0.5); transform: translateY(-2px); }
    .booking-card:hover::before { height: 100%; }

    .booking-card.status-pending::before   { background: #e9c349; }
    .booking-card.status-confirmed::before { background: #4ade80; }
    .booking-card.status-completed::before { background: #60a5fa; }
    .booking-card.status-cancelled::before { background: #584238; }
    .booking-card.status-no_show::before   { background: #f87171; }

    /* Status badge */
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-family: 'Work Sans', sans-serif;
        font-size: 0.65rem; font-weight: 700;
        letter-spacing: 0.12em; text-transform: uppercase;
        padding: 4px 12px; border: 1px solid;
    }
    .badge-pending   { color: #e9c349; border-color: rgba(233,195,73,0.4);   background: rgba(233,195,73,0.08); }
    .badge-confirmed { color: #4ade80; border-color: rgba(74,222,128,0.4);   background: rgba(74,222,128,0.08); }
    .badge-completed { color: #60a5fa; border-color: rgba(96,165,250,0.4);   background: rgba(96,165,250,0.08); }
    .badge-cancelled { color: #a78b7e; border-color: rgba(167,139,126,0.4);  background: rgba(167,139,126,0.08); }
    .badge-no_show   { color: #f87171; border-color: rgba(248,113,113,0.4);  background: rgba(248,113,113,0.08); }

    /* Info item */
    .info-item {
        display: flex; align-items: center; gap: 8px;
        font-size: 0.85rem; color: #e0c0b2;
    }
    .info-item .material-symbols-outlined { font-size: 1rem; color: #ea6b1e; }

    /* Cancel btn */
    .cancel-btn {
        font-family: 'Work Sans', sans-serif;
        font-size: 0.65rem; font-weight: 700;
        letter-spacing: 0.12em; text-transform: uppercase;
        color: #f87171; border: 1px solid rgba(248,113,113,0.3);
        padding: 6px 14px; background: rgba(248,113,113,0.05);
        cursor: pointer; transition: all 0.2s;
    }
    .cancel-btn:hover { background: rgba(248,113,113,0.15); border-color: #f87171; }

    /* Empty state */
    .empty-box {
        border: 1px dashed #584238;
        background: #0d0f0d;
        padding: 80px 24px; text-align: center;
    }

    /* Reveal */
    .reveal { opacity: 0; transform: translateY(20px); transition: opacity 0.5s ease, transform 0.5s ease; }
    .reveal.visible { opacity: 1; transform: none; }

    /* Divider dot */
    .dot-sep { color: #584238; margin: 0 6px; }
</style>
@endsection

@section('content')

{{-- ── Hero ── --}}
<div class="page-hero mt-12">
    <div class="max-w-[900px] mx-auto relative z-10">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-4 reveal">
                    <div class="h-px w-8 bg-primary-container"></div>
                    <span class="text-primary-container font-sans text-xs font-bold tracking-[0.2em] uppercase">Booking</span>
                </div>
                <h1 class="font-serif text-[clamp(2.2rem,5vw,3.5rem)] font-bold text-on-surface leading-tight reveal" style="transition-delay:0.1s">
                    Lịch đặt bàn<br>
                    <span style="color:#ffb693">của tôi</span>
                </h1>
            </div>
            <a href="{{ route('guest.bookings.create') }}"
               class="btn-cta shrink-0 reveal self-start sm:self-auto"
               style="transition-delay:0.15s; ">
                <span class="material-symbols-outlined text-sm align-middle mr-1">add</span>
                Đặt bàn mới
            </a>
        </div>
    </div>
</div>

{{-- ── Flash messages ── --}}
<div class="max-w-[900px] mx-auto px-6 mb-2">
    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 border border-green-800 px-5 py-4 reveal"
             style="background:rgba(74,222,128,0.06)">
            <span class="material-symbols-outlined text-green-400 text-base">check_circle</span>
            <span class="text-green-400 font-sans text-sm">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 flex items-center gap-3 border border-red-800 px-5 py-4 reveal"
             style="background:rgba(248,113,113,0.06)">
            <span class="material-symbols-outlined text-red-400 text-base">error</span>
            <span class="text-red-400 font-sans text-sm">{{ session('error') }}</span>
        </div>
    @endif
</div>

{{-- ── Booking list ── --}}
<div class="max-w-[900px] mx-auto px-6 pb-24">
    <div class="space-y-4">
        @forelse($bookings as $i => $booking)
            @php
                $statusMap = [
                    'pending'   => ['label' => 'Chờ xác nhận', 'badge' => 'badge-pending',   'icon' => 'schedule'],
                    'confirmed' => ['label' => 'Đã xác nhận',  'badge' => 'badge-confirmed',  'icon' => 'check_circle'],
                    'completed' => ['label' => 'Hoàn tất',     'badge' => 'badge-completed',  'icon' => 'done_all'],
                    'cancelled' => ['label' => 'Đã huỷ',       'badge' => 'badge-cancelled',  'icon' => 'cancel'],
                    'no_show'   => ['label' => 'Không đến',    'badge' => 'badge-no_show',    'icon' => 'person_off'],
                ];
                $cfg = $statusMap[$booking->status] ?? $statusMap['pending'];
            @endphp

            <div class="booking-card status-{{ $booking->status }} reveal pl-6 pr-6 pt-5 pb-5"
                 style="transition-delay:{{ $i * 0.06 }}s">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

                    {{-- Left: info --}}
                    <div class="space-y-3 flex-1">
                        {{-- Table name + area --}}
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-serif text-lg font-semibold text-on-surface">
                                {{ $booking->table->name }}
                            </span>
                            <span class="dot-sep">—</span>
                            <span class="info-item">
                                <span class="material-symbols-outlined">location_on</span>
                                {{ $booking->table->area->name }}
                            </span>
                        </div>

                        {{-- Time & date --}}
                        <div class="flex flex-wrap gap-x-5 gap-y-2">
                            <div class="info-item">
                                <span class="material-symbols-outlined">schedule</span>
                                {{ $booking->start_time->format('H:i') }}
                                <span style="color:#584238">→</span>
                                {{ $booking->end_time->format('H:i') }}
                            </div>
                            <div class="info-item">
                                <span class="material-symbols-outlined">calendar_month</span>
                                {{ $booking->start_time->format('d/m/Y') }}
                            </div>
                            <div class="info-item">
                                <span class="material-symbols-outlined">group</span>
                                {{ $booking->guest_count }} người
                            </div>
                        </div>

                        {{-- Note --}}
                        @if($booking->note)
                            <div class="info-item" style="color:#a78b7e">
                                <span class="material-symbols-outlined">sticky_note_2</span>
                                <span class="text-xs italic">{{ $booking->note }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Right: status + cancel --}}

                    <div class="flex flex-row sm:flex-col items-center sm:items-end gap-3 shrink-0">

                        {{-- Badge trạng thái --}}
                        <div class="status-badge {{ $cfg['badge'] }}">
                            <span class="material-symbols-outlined" style="font-size:0.8rem">{{ $cfg['icon'] }}</span>
                            {{ $cfg['label'] }}
                        </div>

                        {{-- Nút huỷ --}}
                        @if($booking->status === 'pending')
                            <form action="{{ route('guest.bookings.destroy', $booking) }}"
                                method="POST"
                                onsubmit="return confirm('Bạn có chắc muốn huỷ đặt bàn này không?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="cancel-btn">
                                    <span class="material-symbols-outlined align-middle" style="font-size:0.8rem">close</span>
                                    Huỷ đặt bàn
                                </button>
                            </form>
                        @endif

                        {{-- Nút review --}}
                        @if($booking->status === 'completed')
                            @if($booking->review)
                                <span style="font-size:0.7rem; color:#4ade80;
                                            letter-spacing:0.1em; text-transform:uppercase;">
                                    ✅ Đã gửi đánh giá
                                </span>
                            @else
                                <button onclick="openReview({{ $booking->id }}, '{{ $booking->table->name }}')"
                                        style="font-family:'Work Sans',sans-serif; font-size:0.65rem; font-weight:700;
                                            letter-spacing:0.12em; text-transform:uppercase; cursor:pointer;
                                            color:#ffb693; border:1px solid rgba(255,182,147,0.3);
                                            padding:6px 14px; background:rgba(255,182,147,0.05);
                                            transition:all 0.2s;">
                                    ⭐ Viết review
                                </button>
                            @endif
                        @endif

                    </div>

                </div>
            </div>

        @empty
            <div class="empty-box reveal">
                <span class="material-symbols-outlined text-5xl block mb-4" style="color:#584238">restaurant</span>
                <p class="font-serif text-xl text-on-surface mb-2">Chưa có đặt bàn nào</p>
                <p class="text-on-surface-variant text-sm mb-8">Hãy để chúng tôi chuẩn bị một bàn hoàn hảo cho bạn.</p>
                <a href="{{ route('guest.bookings.create') }}" class="btn-cta">
                    Đặt bàn ngay
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($bookings->hasPages())
        <div class="mt-8 flex justify-center reveal">
            {{ $bookings->links() }}
        </div>
    @endif
</div>

{{-- ── Modal Review ── --}}
<div id="review-overlay"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7);
            z-index:100; align-items:center; justify-content:center;"
     onclick="closeReview()">

    <div style="background:#1f201e; border:1px solid #584238; padding:32px;
                width:100%; max-width:420px; margin:0 16px; position:relative;"
         onclick="event.stopPropagation()">

        {{-- Header --}}
        <div class="mb-6">
            <h3 class="font-serif text-xl font-bold text-on-surface mb-1">Viết đánh giá</h3>
            <p id="review-subtitle" style="font-size:0.85rem; color:#e0c0b2;"></p>
        </div>

        {{-- Chọn sao --}}
        <div class="mb-5">
            <p style="font-size:0.75rem; font-weight:700; letter-spacing:0.1em;
                      text-transform:uppercase; color:#e0c0b2; margin-bottom:12px;">
                Đánh giá *
            </p>
            <div class="flex gap-2" id="star-container">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button"
                            id="star-{{ $i }}"
                            onclick="setStar({{ $i }})"
                            style="font-size:2rem; color:#584238; background:none;
                                   border:none; cursor:pointer; transition:all 0.15s;
                                   line-height:1;">★</button>
                @endfor
            </div>
            <input type="hidden" id="review-rating" value="0">
            <p id="star-error"
               style="display:none; font-size:0.75rem; color:#f87171; margin-top:6px;">
                Vui lòng chọn số sao
            </p>
        </div>

        {{-- Comment --}}
        <div class="mb-6">
            <p style="font-size:0.75rem; font-weight:700; letter-spacing:0.1em;
                      text-transform:uppercase; color:#e0c0b2; margin-bottom:8px;">
                Nhận xét
            </p>
            <textarea id="review-comment" rows="4"
                      placeholder="Chia sẻ trải nghiệm của bạn..."
                      style="width:100%; background:#121412; border:1px solid #584238;
                             color:#e3e2e0; padding:12px 16px; font-family:'Work Sans',sans-serif;
                             font-size:0.875rem; resize:none; outline:none; transition:border-color 0.2s;"
                      onfocus="this.style.borderColor='#ea6b1e'"
                      onblur="this.style.borderColor='#584238'"></textarea>
        </div>

        {{-- Buttons --}}
        <div class="flex gap-3">
            <button onclick="submitReview()"
                    class="btn-cta flex-1" style="text-align:center;">
                Gửi đánh giá
            </button>
            <button onclick="closeReview()"
                    style="flex:1; padding:10px 24px; border:1px solid #584238;
                           background:transparent; color:#e0c0b2; font-family:'Work Sans',sans-serif;
                           font-size:0.75rem; font-weight:700; letter-spacing:0.15em;
                           text-transform:uppercase; cursor:pointer; transition:all 0.2s;"
                    onmouseover="this.style.borderColor='#e0c0b2'"
                    onmouseout="this.style.borderColor='#584238'">
                Huỷ
            </button>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
    // ── Review Modal ──
    const csrfToken = '{{ csrf_token() }}';
    let selectedStar = 0;
    let currentBookingId = null;
    let currentTableName = null;

    function openReview(bookingId, tableName) {
        currentBookingId = bookingId;
        currentTableName = tableName;
        selectedStar = 0;

        document.getElementById('review-subtitle').textContent = `Bàn ${tableName}`;
        document.getElementById('review-comment').value = '';
        document.getElementById('review-rating').value = 0;
        document.getElementById('star-error').style.display = 'none';
        resetStars();

        const overlay = document.getElementById('review-overlay');
        overlay.style.display = 'flex';
    }

    function closeReview() {
        document.getElementById('review-overlay').style.display = 'none';
    }

    function setStar(star) {
        selectedStar = star;
        document.getElementById('review-rating').value = star;
        highlightStars(star);
    }

    function highlightStars(count) {
        for (let i = 1; i <= 5; i++) {
            const star = document.getElementById(`star-${i}`);
            star.style.color = i <= count ? '#e9c349' : '#584238';
        }
    }

    function resetStars() {
        highlightStars(0);
    }

    // Hover effect
    document.addEventListener('DOMContentLoaded', () => {
        for (let i = 1; i <= 5; i++) {
            const star = document.getElementById(`star-${i}`);
            if (!star) continue;

            star.addEventListener('mouseover', () => highlightStars(i));
            star.addEventListener('mouseout', () => highlightStars(selectedStar));
        }
    });

    async function submitReview() {
        const rating  = parseInt(document.getElementById('review-rating').value);
        const comment = document.getElementById('review-comment').value;

        // Validate
        if (rating === 0) {
            document.getElementById('star-error').style.display = 'block';
            return;
        }

        try {
            const res = await fetch('{{ route('guest.reviews.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    booking_id: currentBookingId,
                    rating,
                    comment,
                }),
            });

            const data = await res.json();

            if (!res.ok) {
                alert(data.error ?? 'Có lỗi xảy ra');
                return;
            }

            // Cập nhật UI — đổi nút thành "Chờ duyệt"
            const btn = document.querySelector(
                `button[onclick="openReview(${currentBookingId}, '${currentTableName}')"]`
            );
            if (btn) {
                btn.outerHTML = `
                    <span style="font-size:0.7rem; color:#4ade80;
                                            letter-spacing:0.1em; text-transform:uppercase;">
                                    ✅ Đã gửi đánh giá
                                </span>`;
            }

            closeReview();

        } catch (err) {
            alert('Lỗi kết nối server');
            console.error(err);
        }
    }
</script>
<script>
    const els = document.querySelectorAll('.reveal');
    const io = new IntersectionObserver(e => e.forEach(x => { if(x.isIntersecting){ x.target.classList.add('visible'); io.unobserve(x.target); }}), {threshold:0.08});
    els.forEach(el => io.observe(el));
    document.querySelectorAll('.page-hero .reveal').forEach((el,i) => setTimeout(() => el.classList.add('visible'), 80+i*140));
</script>
@endsection