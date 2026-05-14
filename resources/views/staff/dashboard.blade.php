@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

<style>
    .stat-card { position: relative; overflow: hidden; }
    .stat-card::before {
        content: '';
        position: absolute; top: 0; left: 0;
        width: 3px; height: 100%;
        background: var(--accent-color, #f5a623);
        border-radius: 3px 0 0 3px;
    }
    .shift-panel { display: none; }
    .shift-panel.active { display: flex; }
    .staff-avatar-chip {
        width: 24px; height: 24px; border-radius: 50%;
        background: linear-gradient(135deg, #c8622a, #f5a623);
        color: #fff; font-size: 9px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .pulse-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #22c55e;
        animation: pulse 2s infinite;
        flex-shrink: 0;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.5; transform: scale(1.3); }
    }
    .booking-row:last-child, .order-row:last-child { border-bottom: none !important; }
    .nav-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: rgba(255,255,255,0.35); cursor: pointer; transition: all 0.2s;
    }
    .nav-dot.active { background: #f5a623; transform: scale(1.3); }
    .walkin-btn {
        background: linear-gradient(135deg, #f5a623, #c8622a);
        transition: opacity 0.2s, transform 0.15s;
    }
    .walkin-btn:hover { opacity: 0.92; transform: translateY(-1px); }
</style>

{{-- Welcome bar --}}
<div class="flex items-center justify-between mb-5">
    <div>
        <h1 class="text-lg font-semibold text-gray-800">
            Xin chào, {{ Auth::user()->name }} 👋
        </h1>
        <p class="text-sm text-gray-400 mt-0.5">
            {{ \Carbon\Carbon::today()->isoFormat('dddd, DD/MM/YYYY') }}
        </p>
    </div>
</div>

{{-- ══════════════════════════════════════
     TẦNG 1 — Ca làm (2/3) | Walk-in (1/3)
══════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

    {{-- Ca làm hôm nay — chiếm 2/3 --}}
    <div class="lg:col-span-2 rounded-xl p-5 flex flex-col justify-between"
         style="background: linear-gradient(135deg, #2c1a0e, #5c3317); min-height: 120px;">

        @if($mySchedules->isEmpty())
            <div class="text-center py-4">
                <p class="text-orange-200 text-sm">Bạn không có ca làm việc hôm nay.</p>
                <a href="{{ route('staff.schedules.index') }}"
                   class="text-orange-400 text-xs hover:underline mt-1 inline-block">Xem lịch tuần →</a>
            </div>
        @else
            {{-- Header: label + điều hướng nếu nhiều ca --}}
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-orange-300 uppercase tracking-wide">
                    Ca làm hôm nay
                </span>
                @if($mySchedules->count() > 1)
                    <div class="flex items-center gap-3">
                        <button onclick="prevShift()"
                                class="text-orange-300 hover:text-white transition text-lg leading-none">‹</button>
                        <div class="flex gap-1.5" id="shift-dots">
                            @foreach($mySchedules as $i => $s)
                                <div class="nav-dot {{ $i === 0 ? 'active' : '' }}"
                                     onclick="goShift({{ $i }})"></div>
                            @endforeach
                        </div>
                        <button onclick="nextShift()"
                                class="text-orange-300 hover:text-white transition text-lg leading-none">›</button>
                    </div>
                @endif
            </div>

            {{-- Các panel ca --}}
            @foreach($mySchedules as $i => $shift)
                <div class="shift-panel items-center justify-between {{ $i === 0 ? 'active' : '' }}"
                     data-index="{{ $i }}">
                    <div class="flex items-center gap-3">
                        @if($shift['is_active'])
                            <div class="pulse-dot"></div>
                        @else
                            <div class="w-2 h-2 rounded-full flex-shrink-0"
                                 style="background: {{ $shift['is_past'] ? '#6b7280' : '#f5a623' }}"></div>
                        @endif
                        <div>
                            <div class="text-white font-bold text-base">{{ $shift['name'] }}</div>
                            <div class="text-orange-200 text-sm mt-0.5">
                                {{ $shift['start'] }} — {{ $shift['end'] }}
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($shift['is_active'])
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold
                                         bg-green-500 bg-opacity-20 text-green-300">
                                Đang làm việc
                            </span>
                            <div class="text-orange-300 text-xs mt-1">
                                Kết thúc sau {{ $shift['diff_human'] }}
                            </div>
                        @elseif($shift['is_past'])
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                         bg-gray-500 bg-opacity-20 text-gray-300">
                                Đã kết thúc
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                         bg-orange-500 bg-opacity-20 text-orange-300">
                                Chưa bắt đầu
                            </span>
                            <div class="text-orange-300 text-xs mt-1">
                                Bắt đầu sau {{ $shift['diff_human'] }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Walk-in Guest — chiếm 1/3 --}}
    <a href="{{ route('manage.bookings.create') }}"
       class="walkin-btn rounded-xl p-5 flex flex-col items-center justify-center gap-3 text-center no-underline"
       style="min-height: 120px;">
        <div class="w-12 h-12 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
        </div>
        <div>
            <div class="text-white font-bold text-base">Walk-in Guest</div>
            <div class="text-orange-100 text-xs mt-0.5">Tạo đặt bàn trực tiếp</div>
        </div>
    </a>

</div>

{{-- ══════════════════════════════════════
     TẦNG 2 — 3 Stat cards
══════════════════════════════════════ --}}
<div class="grid grid-cols-3 gap-4 mb-6">

    <div class="stat-card bg-white rounded-xl p-4 shadow-sm border border-gray-100"
         style="--accent-color: #f5a623;">
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Booking chờ xử lý
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $bookingsPending }}</div>
        <div class="text-xs mt-1 {{ $bookingsPending > 0 ? 'text-orange-500 font-medium' : 'text-gray-400' }}">
            {{ $bookingsPending > 0 ? 'Cần xác nhận ngay' : 'Không có' }}
        </div>
    </div>

    <div class="stat-card bg-white rounded-xl p-4 shadow-sm border border-gray-100"
         style="--accent-color: #22c55e;">
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Booking đã xác nhận
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $bookingsConfirmed }}</div>
        <div class="text-xs text-gray-400 mt-1">Hôm nay</div>
    </div>

    <div class="stat-card bg-white rounded-xl p-4 shadow-sm border border-gray-100"
         style="--accent-color: #c8622a;">
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Order đang mở
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $ordersOpen }}</div>
        <div class="text-xs text-gray-400 mt-1">Đang phục vụ</div>
    </div>

</div>

{{-- ══════════════════════════════════════
     TẦNG 3 — Booking pending + Order mở
══════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

    {{-- Booking cần xử lý --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Booking chờ xác nhận
                @if($bookingsPending > 0)
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-xs font-bold text-white"
                          style="background:#f5a623;">
                        {{ $bookingsPending }}
                    </span>
                @endif
            </h2>
            <a href="{{ route('manage.bookings.index') }}"
               class="text-xs text-orange-500 hover:text-orange-600 font-medium transition">
                Xem tất cả →
            </a>
        </div>

        <div class="overflow-y-auto" style="max-height: 300px;">
            @forelse($pendingBookings as $booking)
                <div class="booking-row flex items-start gap-3 py-2.5 border-b border-gray-50">
                    <div class="text-xs font-bold text-orange-600 flex-shrink-0 pt-0.5 w-10">
                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm text-gray-700 font-medium truncate">
                            {{ $booking->guest_name ?? $booking->user?->name ?? 'Khách vãng lai' }}
                            <span class="text-gray-400 font-normal text-xs">
                                · {{ $booking->guest_count ?? '?' }} khách
                            </span>
                        </div>
                        <div class="text-xs text-gray-400 mt-0.5">
                            {{ $booking->table?->name ?? 'Chưa có bàn' }}
                            @if($booking->table?->area)
                                — {{ $booking->table->area->name }}
                            @endif
                        </div>
                    </div>
                    <form action="{{ route('manage.bookings.confirm', $booking) }}"
                          method="POST" class="flex-shrink-0">
                        @csrf
                        <button type="submit"
                                class="px-3 py-1 rounded-lg text-xs font-semibold text-white transition hover:opacity-90"
                                style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                            Xác nhận
                        </button>
                    </form>
                </div>
            @empty
                <div class="text-center py-10">
                    <p class="text-sm text-gray-400">Không có booking nào chờ xử lý</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Order đang mở --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <div class="pulse-dot"></div>
                Order đang mở
            </h2>
            <a href="{{ route('manage.orders.index') }}"
               class="text-xs text-orange-500 hover:text-orange-600 font-medium transition">
                Xem tất cả →
            </a>
        </div>

        <div class="overflow-y-auto" style="max-height: 300px;">
            @forelse($openOrders as $order)
                <div class="order-row flex items-center gap-3 py-2.5 border-b border-gray-50">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-bold"
                         style="background:#fdf0dc; color:#c8622a;">
                        {{ $order->table?->name ?? 'N/A' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm text-gray-700 font-medium truncate">
                            {{ $order->booking?->guest_name ?? $order->booking?->user?->name ?? 'Khách vãng lai' }}
                        </div>
                        <div class="text-xs text-gray-400 mt-0.5">
                            {{ $order->orderItems->count() }} món
                            · {{ $order->table?->area?->name ?? '' }}
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <div class="text-sm font-semibold text-gray-700">
                            {{ number_format($order->total ?? 0, 0, ',', '.') }}₫
                        </div>
                        <a href="{{ route('manage.orders.show', $order) }}"
                           class="text-xs text-orange-500 hover:text-orange-600 font-medium transition">
                            Xem →
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <p class="text-sm text-gray-400">Không có order đang mở</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════
     TẦNG 4 — Lịch ca toàn nhân viên hôm nay
══════════════════════════════════════ --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Nhân viên làm việc hôm nay
        </h2>
        <a href="{{ route('staff.schedules.index') }}"
           class="text-xs text-orange-500 hover:text-orange-600 font-medium transition">
            Xem lịch tuần →
        </a>
    </div>

    @forelse($allSchedulesToday as $shiftName => $schedules)
        <div class="mb-4 last:mb-0">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">{{ $shiftName }}</span>
                <span class="text-xs text-gray-300">
                    · {{ \Carbon\Carbon::today()->setTimeFromTimeString($schedules->first()->shift->start_time)->format('H:i') }}
                    — {{ \Carbon\Carbon::today()->setTimeFromTimeString($schedules->first()->shift->end_time)->format('H:i') }}
                </span>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($schedules as $schedule)
                    <div class="flex items-center gap-2 rounded-full px-3 py-1.5 border text-xs
                        {{ $schedule->user_id === Auth::id()
                            ? 'bg-orange-50 border-orange-200 text-orange-700 font-semibold'
                            : 'bg-gray-50 border-gray-100 text-gray-600' }}">
                        <div class="staff-avatar-chip">
                            {{ strtoupper(substr($schedule->user->name, 0, 2)) }}
                        </div>
                        {{ $schedule->user->name }}
                        @if($schedule->user_id === Auth::id())
                            <span class="text-orange-400">(bạn)</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-400 text-center py-6">Chưa có lịch phân ca hôm nay</p>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
(function () {
    const total = {{ $mySchedules->count() }};
    if (total <= 1) return;

    let current = 0;

    function goShift(index) {
        document.querySelectorAll('.shift-panel').forEach((p, i) => {
            p.classList.toggle('active', i === index);
        });
        document.querySelectorAll('.nav-dot').forEach((d, i) => {
            d.classList.toggle('active', i === index);
        });
        current = index;
    }

    window.prevShift = () => goShift((current - 1 + total) % total);
    window.nextShift = () => goShift((current + 1) % total);
    window.goShift   = goShift;
})();
</script>
@endpush