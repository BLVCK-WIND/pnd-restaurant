@extends('layouts.admin')
@section('title', 'Doanh thu')

@section('content')
<div>

    {{-- ── Page Header ── --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📊 Thống kê doanh thu</h1>
        <span class="text-xs text-gray-400">Cập nhật: {{ now()->format('H:i — d/m/Y') }}</span>
    </div>

    {{-- ── Chọn chế độ xem: Tuần / Tháng ── --}}
    <div class="bg-white rounded-2xl shadow p-4 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">

            {{-- Tab chọn mode --}}
            <div class="flex gap-2">
                <a href="{{ route('admin.revenue.index', ['mode' => 'week']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition
                          {{ $mode === 'week'
                              ? 'text-white'
                              : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                   @if($mode === 'week') style="background: linear-gradient(135deg, #c8622a, #f5a623);" @endif>
                    📆 Theo tuần
                </a>
                <a href="{{ route('admin.revenue.index', ['mode' => 'month']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition
                          {{ $mode === 'month'
                              ? 'text-white'
                              : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                   @if($mode === 'month') style="background: linear-gradient(135deg, #c8622a, #f5a623);" @endif>
                    🗓️ Theo tháng
                </a>
            </div>

            {{-- Điều hướng tuần / tháng --}}
            @if($mode === 'week')
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.revenue.index', ['mode' => 'week', 'week' => $prevWeek->format('Y-m-d')]) }}"
                       class="px-3 py-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition text-sm">
                        ← Tuần trước
                    </a>
                    <div class="text-center">
                        <p class="font-semibold text-gray-700 text-sm">
                            {{ $weekStart->format('d/m') }} — {{ $weekEnd->format('d/m/Y') }}
                        </p>
                        @if($weekStart->isCurrentWeek())
                            <p class="text-xs text-orange-500">Tuần hiện tại</p>
                        @else
                            <p class="text-xs text-gray-400">Tuần đã qua</p>
                        @endif
                    </div>
                    @if($canGoNext)
                        <a href="{{ route('admin.revenue.index', ['mode' => 'week', 'week' => $nextWeek->format('Y-m-d')]) }}"
                           class="px-3 py-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition text-sm">
                            Tuần sau →
                        </a>
                    @else
                        <span class="px-3 py-2 rounded-xl bg-gray-50 text-gray-300 text-sm cursor-not-allowed">
                            Tuần sau →
                        </span>
                    @endif
                    @if(!$weekStart->isCurrentWeek())
                        <a href="{{ route('admin.revenue.index', ['mode' => 'week']) }}"
                           class="px-3 py-2 rounded-xl text-white text-sm"
                           style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                            Tuần này
                        </a>
                    @endif
                </div>

            @else {{-- month --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.revenue.index', ['mode' => 'month', 'month' => $prevMonth->format('Y-m')]) }}"
                       class="px-3 py-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition text-sm">
                        ← Tháng trước
                    </a>
                    <div class="text-center">
                        <p class="font-semibold text-gray-700 text-sm">
                            Tháng {{ $currentMonth->month }}/{{ $currentMonth->year }}
                        </p>
                        @if($currentMonth->isCurrentMonth())
                            <p class="text-xs text-orange-500">Tháng hiện tại</p>
                        @else
                            <p class="text-xs text-gray-400">Tháng đã qua</p>
                        @endif
                    </div>
                    @if($canGoNext)
                        <a href="{{ route('admin.revenue.index', ['mode' => 'month', 'month' => $nextMonth->format('Y-m')]) }}"
                           class="px-3 py-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition text-sm">
                            Tháng sau →
                        </a>
                    @else
                        <span class="px-3 py-2 rounded-xl bg-gray-50 text-gray-300 text-sm cursor-not-allowed">
                            Tháng sau →
                        </span>
                    @endif
                    @if(!$currentMonth->isCurrentMonth())
                        <a href="{{ route('admin.revenue.index', ['mode' => 'month']) }}"
                           class="px-3 py-2 rounded-xl text-white text-sm"
                           style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                            Tháng này
                        </a>
                    @endif
                </div>
            @endif

        </div>
    </div>

    {{-- ── Thẻ tổng quan ── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        {{-- Tổng doanh thu kỳ đang xem --}}
        <div class="bg-white rounded-2xl shadow p-5">
            <p class="text-sm text-gray-500 font-medium mb-3">
                {{ $mode === 'week' ? 'Tổng tuần này' : 'Tổng tháng này' }}
            </p>
            <p class="text-2xl font-bold text-gray-800 mb-2">
                {{ $mode === 'week'
                    ? number_format($revenueThisWeek)
                    : number_format($revenueThisMonth) }} đ
            </p>
            <div class="flex items-center gap-1 text-xs">
                @php $change = $mode === 'week' ? $changeWeek : $changeMonth; @endphp
                @if($change['up'])
                    <span class="text-green-600 font-semibold">▲ {{ $change['value'] }}%</span>
                @else
                    <span class="text-red-500 font-semibold">▼ {{ $change['value'] }}%</span>
                @endif
                <span class="text-gray-400">
                    so với {{ $mode === 'week' ? 'tuần' : 'tháng' }} trước
                    ({{ $mode === 'week'
                        ? number_format($revenueLastWeek)
                        : number_format($revenueLastMonth) }} đ)
                </span>
            </div>
        </div>

        {{-- Tổng số order --}}
        <div class="bg-white rounded-2xl shadow p-5">
            <p class="text-sm text-gray-500 font-medium mb-3">Số order</p>
            <p class="text-2xl font-bold text-gray-800 mb-2">
                {{ $dailyRevenue->sum('count') }}
            </p>
            <p class="text-xs text-gray-400">
                Trung bình:
                @php
                    $totalRevenue = $mode === 'week' ? $revenueThisWeek : $revenueThisMonth;
                    $totalOrders  = $dailyRevenue->sum('count');
                @endphp
                {{ $totalOrders > 0 ? number_format($totalRevenue / $totalOrders) : 0 }} đ/order
            </p>
        </div>

        {{-- Ngày cao nhất --}}
        <div class="bg-white rounded-2xl shadow p-5">
            <p class="text-sm text-gray-500 font-medium mb-3">Ngày doanh thu cao nhất</p>
            @if($dailyRevenue->isNotEmpty())
                @php $bestDay = $dailyRevenue->sortByDesc('total')->first(); @endphp
                <p class="text-2xl font-bold text-gray-800 mb-2">
                    {{ number_format($bestDay->total) }} đ
                </p>
                <p class="text-xs text-gray-400">
                    {{ \Carbon\Carbon::parse($bestDay->date)->format('d/m/Y') }}
                </p>
            @else
                <p class="text-2xl font-bold text-gray-300 mb-2">—</p>
                <p class="text-xs text-gray-400">Chưa có dữ liệu</p>
            @endif
        </div>

    </div>

    {{-- ── Bảng chi tiết + Phương thức ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Bảng doanh thu từng ngày --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800 text-sm">
                    Doanh thu từng ngày
                </h2>
                <span class="text-xs text-gray-400">{{ $dailyRevenue->count() }} ngày có doanh thu</span>
            </div>

            @if($dailyRevenue->isEmpty())
                <div class="px-5 py-12 text-center text-gray-400 text-sm">
                    Chưa có doanh thu trong kỳ này
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead style="background: #2c1a0e;">
                            <tr>
                                <th class="px-5 py-3 text-white font-medium text-left">Ngày</th>
                                <th class="px-5 py-3 text-white font-medium text-center">Số order</th>
                                <th class="px-5 py-3 text-white font-medium text-right">Doanh thu</th>
                                <th class="px-5 py-3 text-white font-medium text-right">Tỉ lệ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($dailyRevenue as $day)
                                @php
                                    $totalKy  = $mode === 'week' ? $revenueThisWeek : $revenueThisMonth;
                                    $percent  = $totalKy > 0 ? round(($day->total / $totalKy) * 100, 1) : 0;
                                    $isToday  = \Carbon\Carbon::parse($day->date)->isToday();
                                @endphp
                                <tr class="{{ $isToday ? 'bg-orange-50' : 'hover:bg-gray-50' }} transition">
                                    <td class="px-5 py-3 text-gray-700">
                                        {{ \Carbon\Carbon::parse($day->date)->locale('vi')->isoFormat('dddd, DD/MM/YYYY') }}
                                        @if($isToday)
                                            <span class="ml-2 px-2 py-0.5 rounded-full text-xs
                                                         bg-orange-100 text-orange-600 font-medium">
                                                Hôm nay
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-center text-gray-500">
                                        {{ $day->count }}
                                    </td>
                                    <td class="px-5 py-3 text-right font-semibold text-gray-800">
                                        {{ number_format($day->total) }} đ
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <div class="w-20 bg-gray-100 rounded-full h-1.5">
                                                <div class="h-1.5 rounded-full"
                                                     style="width: {{ $percent }}%;
                                                            background: linear-gradient(135deg, #c8622a, #f5a623);">
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-500 w-10 text-right">
                                                {{ $percent }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-200 bg-gray-50">
                                <td class="px-5 py-3 font-semibold text-gray-700">Tổng</td>
                                <td class="px-5 py-3 text-center font-semibold text-gray-700">
                                    {{ $dailyRevenue->sum('count') }}
                                </td>
                                <td class="px-5 py-3 text-right font-bold text-gray-800">
                                    {{ number_format($totalKy) }} đ
                                </td>
                                <td class="px-5 py-3 text-right text-xs text-gray-400">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

        {{-- Phương thức thanh toán --}}
        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800 text-sm">Phương thức thanh toán</h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $mode === 'week'
                        ? $weekStart->format('d/m') . ' — ' . $weekEnd->format('d/m/Y')
                        : 'Tháng ' . $currentMonth->format('m/Y') }}
                </p>
            </div>

            @php
                $methodIcons = [
                    'cash'     => ['icon' => '💵', 'label' => 'Tiền mặt',    'color' => 'bg-green-100 text-green-700'],
                    'card'     => ['icon' => '💳', 'label' => 'Thẻ',          'color' => 'bg-blue-100 text-blue-700'],
                    'transfer' => ['icon' => '🏦', 'label' => 'Chuyển khoản', 'color' => 'bg-purple-100 text-purple-700'],
                ];
                $totalAllMethods = $paymentMethods->sum('total');
            @endphp

            @if($paymentMethods->isEmpty())
                <div class="px-5 py-12 text-center text-gray-400 text-sm">Chưa có dữ liệu</div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($paymentMethods as $method)
                        @php
                            $meta    = $methodIcons[$method->method] ?? ['icon' => '💰', 'label' => ucfirst($method->method), 'color' => 'bg-gray-100 text-gray-700'];
                            $percent = $totalAllMethods > 0 ? round(($method->total / $totalAllMethods) * 100, 1) : 0;
                        @endphp
                        <div class="px-5 py-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span>{{ $meta['icon'] }}</span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">{{ $meta['label'] }}</p>
                                        <p class="text-xs text-gray-400">{{ $method->count }} lần</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $meta['color'] }}">
                                    {{ $percent }}%
                                </span>
                            </div>
                            <p class="text-sm font-semibold text-gray-800 mb-1.5">
                                {{ number_format($method->total) }} đ
                            </p>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full"
                                     style="width: {{ $percent }}%; background: linear-gradient(135deg, #c8622a, #f5a623);">
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="px-5 py-4 bg-gray-50 flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-700">Tổng cộng</p>
                        <p class="text-sm font-bold text-gray-800">{{ number_format($totalAllMethods) }} đ</p>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection