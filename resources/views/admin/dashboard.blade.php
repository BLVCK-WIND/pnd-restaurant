@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

<style>
    .stat-card::before {
        content: '';
        position: absolute; top: 0; left: 0;
        width: 3px; height: 100%;
        background: var(--accent-color, #f5a623);
        border-radius: 3px 0 0 3px;
    }
    .stat-card {
        position: relative;
        overflow: hidden;
    }
    .top-bar-fill {
        height: 4px;
        border-radius: 2px;
        background: linear-gradient(90deg, #f5a623, #c8622a);
    }
    .toggle-tabs button.active {
        background: #f5a623;
        color: #2c1a0e;
        font-weight: 600;
    }
    .toggle-tabs button {
        transition: all 0.15s ease;
    }
    .staff-avatar-chip {
        width: 22px; height: 22px;
        border-radius: 50%;
        background: linear-gradient(135deg, #c8622a, #f5a623);
        color: #fff;
        font-size: 9px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .booking-row:last-child {
        border-bottom: none !important;
    }
    .apexcharts-tooltip {
        border: 1px solid #f5a623 !important;
        box-shadow: 0 4px 12px rgba(200,98,42,0.15) !important;
    }
</style>

{{-- Welcome bar --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-lg font-semibold text-gray-800">Tổng quan hôm nay</h1>
        <p class="text-sm text-gray-400 mt-0.5">{{ \Carbon\Carbon::today()->isoFormat('dddd, DD/MM/YYYY') }}</p>
    </div>
    <a href="{{ route('admin.revenue.index') }}"
       class="flex items-center gap-2 text-sm text-orange-600 font-medium hover:text-orange-700 transition">
        Xem báo cáo chi tiết
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
</div>

{{-- ══════════════════════════════════════
     TẦNG 1 — 4 Stat Cards
══════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Doanh thu --}}
    <div class="stat-card bg-white rounded-xl p-4 shadow-sm border border-gray-100"
         style="--accent-color: #f5a623;">
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Doanh thu tháng này
        </div>
        <div class="text-2xl font-bold text-gray-800">
            {{ number_format($revenueThisMonth, 0, ',', '.') }}₫
        </div>
        <div class="text-xs text-gray-400 mt-1">
            @if($revenueGrowth !== null)
                               {{ $revenueGrowth > 0 ? '↑' : '↓' }} {{ abs($revenueGrowth) }}% so với tháng trước
                             @endif
        </div>
    </div>

    {{-- Order --}}
    <div class="stat-card bg-white rounded-xl p-4 shadow-sm border border-gray-100"
         style="--accent-color: #c8622a;">
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Doanh thu hôm nay
        </div>
        <div class="text-2xl font-bold text-gray-800">
            {{ number_format($revenueToday, 0, ',', '.') }}₫
        </div>
        <div class="text-xs text-gray-400 mt-1">{{ $ordersToday }} order hôm nay</div>
    </div>

    {{-- Booking --}}
    <div class="stat-card bg-white rounded-xl p-4 shadow-sm border border-gray-100"
         style="--accent-color: #5c3317;">
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
            <svg class="w-4 h-4 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Đặt bàn hôm nay
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $bookingsToday }}</div>
        <div class="text-xs text-gray-400 mt-1">Pending &amp; đã xác nhận</div>
    </div>

    {{-- Review --}}
    <div class="stat-card bg-white rounded-xl p-4 shadow-sm border border-gray-100"
         style="--accent-color: #888;">
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            Review chờ duyệt
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $reviewsPending }}</div>
        <a href="{{ route('admin.reviews.index') }}"
           class="text-xs text-orange-500 hover:text-orange-600 font-medium mt-1 block transition">
            Xem &amp; phê duyệt →
        </a>
    </div>

</div>

{{-- ══════════════════════════════════════
     TẦNG 2 — Biểu đồ + Ca làm việc
══════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

    {{-- Biểu đồ doanh thu (chiếm 2/3) --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Doanh thu
            </h2>
            <div class="toggle-tabs flex border border-gray-200 rounded-lg overflow-hidden text-xs">
                <button id="btn-week"
                        onclick="switchChart('week')"
                        class="active px-3 py-1.5 text-gray-600 bg-orange-400 font-medium transition">
                    Tuần này
                </button>
                <button id="btn-month"
                        onclick="switchChart('month')"
                        class="px-3 py-1.5 text-gray-600 hover:bg-gray-50 transition">
                    Tháng này
                </button>
            </div>
        </div>
        <div id="revenue-chart" style="height: 230px;"></div>
    </div>

    {{-- Ca làm việc hôm nay (chiếm 1/3) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Ca làm hôm nay
        </h2>

        @forelse($staffToday as $shiftName => $schedules)
            <div class="mb-4 last:mb-0">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">
                    {{ $shiftName }}
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($schedules as $schedule)
                        <div class="flex items-center gap-1.5 bg-orange-50 border border-orange-100 rounded-full px-2.5 py-1">
                            <div class="staff-avatar-chip">
                                {{ strtoupper(substr($schedule->user->name, 0, 2)) }}
                            </div>
                            <span class="text-xs text-gray-700">{{ $schedule->user->name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400 text-center py-8">Chưa có lịch phân ca</p>
        @endforelse
    </div>

</div>

{{-- ══════════════════════════════════════
     TẦNG 3 — Top món + Booking
══════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

    {{-- Top 5 món bán chạy --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
            </svg>
            Top 5 món bán chạy hôm nay
        </h2>

        @php $maxSold = $topMenuItems->max('sold_today') ?: 1; @endphp

        @forelse($topMenuItems as $i => $item)
            <div class="flex items-center gap-3 py-2.5 border-b border-gray-50 last:border-0">
                {{-- Rank --}}
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                    {{ $i === 0 ? 'bg-orange-400 text-white' : 'bg-orange-50 text-orange-600' }}">
                    {{ $i + 1 }}
                </div>
                {{-- Tên --}}
                <div class="flex-1 min-w-0">
                    <div class="text-sm text-gray-700 truncate">{{ $item->name }}</div>
                    <div class="mt-1 h-1 bg-gray-100 rounded-full overflow-hidden" style="width: 100%;">
                        <div class="top-bar-fill h-full rounded-full"
                             style="width: {{ round(($item->sold_today / $maxSold) * 100) }}%"></div>
                    </div>
                </div>
                {{-- Số --}}
                <div class="text-sm font-semibold text-gray-600 flex-shrink-0">{{ $item->sold_today }}</div>
            </div>
        @empty
            <p class="text-sm text-gray-400 text-center py-8">Chưa có dữ liệu hôm nay</p>
        @endforelse
    </div>

    {{-- Booking hôm nay --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Đặt bàn hôm nay
            </h2>
            <a href="{{ route('manage.bookings.index') }}"
               class="text-xs text-orange-500 hover:text-orange-600 font-medium transition">
                Xem tất cả →
            </a>
        </div>

        <div class="overflow-y-auto" style="max-height: 280px;">
            @forelse($bookings as $booking)
                <div class="booking-row flex items-start gap-3 py-2.5 border-b border-gray-50">
                    {{-- Giờ --}}
                    <div class="text-xs font-bold text-orange-600 flex-shrink-0 pt-0.5 w-10">
                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                    </div>
                    {{-- Thông tin --}}
                    <div class="flex-1 min-w-0">
                        <div class="text-sm text-gray-700 font-medium truncate">
                            {{ $booking->user->name ?? 'Khách vãng lai' }}
                            <span class="text-gray-400 font-normal text-xs">
                                · {{ $booking->guest_count ?? '?' }} khách
                            </span>
                        </div>
                        <div class="text-xs text-gray-400 mt-0.5">
                            {{ $booking->table->name ?? '' }}
                            @if($booking->table && $booking->table->area)
                                — {{ $booking->table->area->name }}
                            @endif
                        </div>
                    </div>
                    {{-- Status --}}
                    <div class="flex-shrink-0">
                        @if($booking->status === 'confirmed')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">
                                Xác nhận
                            </span>
                        @elseif($booking->status === 'pending')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-600">
                                Chờ
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                {{ $booking->status }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-8">Không có đặt bàn hôm nay</p>
            @endforelse
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.0/dist/apexcharts.min.js"></script>
<script>
(function () {
    // ── Dữ liệu từ controller ──────────────────────────────
    const weekData = {
        labels: @json($last7Days->pluck('date')),
        values: @json($last7Days->pluck('total'))
    };

    // Dữ liệu tháng: nhóm last7Days thành 4 tuần (placeholder thực tế bạn có thể
    // truyền $last4Weeks từ controller tương tự $last7Days)
    const monthData = {
        labels: @json($last4Weeks->pluck('date')),
        values: @json($last4Weeks->pluck('total'))
    };

    // ── Cấu hình ApexCharts ────────────────────────────────
    function buildOptions(data) {
        return {
            chart: {
                type: 'line',
                height: 230,
                toolbar: { show: false },
                zoom: { enabled: false },
                animations: { enabled: true, speed: 400 },
                fontFamily: 'inherit',
            },
            series: [{
                name: 'Doanh thu',
                data: data.values
            }],
            xaxis: {
                categories: data.labels,
                labels: {
                    style: { colors: '#9ca3af', fontSize: '11px' }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#9ca3af', fontSize: '11px' },
                    formatter: v => {
                        if (v >= 1000000) return (v / 1000000).toFixed(1) + 'M';
                        if (v >= 1000)    return (v / 1000).toFixed(0) + 'K';
                        return v;
                    }
                }
            },
            stroke: {
                curve: 'smooth',
                width: 2.5,
                colors: ['#f5a623']
            },
            markers: {
                size: 5,
                colors: ['#fff'],
                strokeColors: ['#f5a623'],
                strokeWidth: 2.5,
                hover: { size: 7 }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.18,
                    opacityTo: 0.01,
                    stops: [0, 100],
                    colorStops: [{
                        offset: 0, color: '#f5a623', opacity: 0.8
                    }, {
                        offset: 100, color: '#f5a623', opacity: 0.4
                    }]
                }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: v => new Intl.NumberFormat('vi-VN').format(v) + '₫'
                }
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
                xaxis: { lines: { show: false } }
            },
            colors: ['#f5a623'],
            legend: { show: false },
            dataLabels: { enabled: false }
        };
    }

    // ── Khởi tạo chart ─────────────────────────────────────
    let chart = new ApexCharts(document.getElementById('revenue-chart'), buildOptions(weekData));
    chart.render();

    // ── Toggle tuần / tháng ────────────────────────────────
    window.switchChart = function (mode) {
        const btnWeek  = document.getElementById('btn-week');
        const btnMonth = document.getElementById('btn-month');
        if (mode === 'week') {
            btnWeek.classList.add('active', 'bg-orange-400', 'text-amber-900', 'font-semibold');
            btnMonth.classList.remove('active', 'bg-orange-400', 'text-amber-900', 'font-semibold');
            chart.updateOptions(buildOptions(weekData), true, true);
        } else {
            btnMonth.classList.add('active', 'bg-orange-400', 'text-amber-900', 'font-semibold');
            btnWeek.classList.remove('active', 'bg-orange-400', 'text-amber-900', 'font-semibold');
            chart.updateOptions(buildOptions(monthData), true, true);
        }
    };
})();
</script>
@endpush