@extends('layouts.manage')

@section('title', 'Quản lý Order')

@section('content')
<div>

    {{-- ── Top bar ── --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Quản lý Order</h1>
    </div>

    {{-- ══ DAY NAVIGATOR ══ --}}
    <div class="bg-white rounded-2xl shadow p-4 mb-5">
        <div class="flex items-center gap-2">

            {{-- Prev --}}
            @if($canGoPrev)
                <a href="{{ route('manage.orders.index', array_merge(request()->except('date','page'), ['date' => $prevDate->format('Y-m-d')])) }}"
                   class="flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200
                          text-gray-400 hover:bg-orange-50 hover:border-orange-300 hover:text-orange-500 transition shrink-0">
                    ‹
                </a>
            @else
                <span class="flex items-center justify-center w-9 h-9 rounded-xl border border-gray-100
                             text-gray-200 cursor-not-allowed shrink-0">‹</span>
            @endif

            {{-- Week pills --}}
            @php
                $weekStart = $selectedDate->copy()->startOfWeek();
                $vnDays    = ['CN','T2','T3','T4','T5','T6','T7'];
            @endphp
            <div class="flex gap-1 flex-1 overflow-x-auto">
                @for($d = 0; $d < 7; $d++)
                    @php
                        $day      = $weekStart->copy()->addDays($d);
                        $isActive = $day->isSameDay($selectedDate);
                        $isToday2 = $day->isToday();
                        $inRange  = $day->between($minDate, $maxDate);
                    @endphp
                    @if($inRange)
                        <a href="{{ route('manage.orders.index', array_merge(request()->except('date','page'), ['date' => $day->format('Y-m-d')])) }}"
                           class="flex flex-col items-center px-3 py-2 rounded-xl min-w-[48px] flex-1 transition text-center
                                  {{ $isActive ? 'text-white' : 'text-gray-500 hover:bg-orange-50 hover:text-orange-500' }}"
                           style="{{ $isActive ? 'background: linear-gradient(135deg, #c8622a, #f5a623);' : '' }}">
                            <span class="text-[10px] font-semibold uppercase tracking-wide
                                         {{ $isActive ? 'text-orange-100' : 'text-gray-400' }}">
                                {{ $vnDays[$day->dayOfWeek] }}
                            </span>
                            <span class="text-sm font-bold mt-0.5
                                         {{ $isToday2 && !$isActive ? 'text-orange-500' : '' }}">
                                {{ $day->format('d') }}
                            </span>
                            @if($isToday2)
                                <span class="w-1 h-1 rounded-full mt-0.5
                                             {{ $isActive ? 'bg-white' : 'bg-orange-400' }}"></span>
                            @endif
                        </a>
                    @else
                        {{-- Ngày tương lai → mờ, không bấm được --}}
                        <span class="flex flex-col items-center px-3 py-2 rounded-xl min-w-[48px] flex-1 text-center opacity-20 cursor-not-allowed">
                            <span class="text-[10px] font-semibold uppercase tracking-wide text-gray-300">{{ $vnDays[$day->dayOfWeek] }}</span>
                            <span class="text-sm font-bold mt-0.5 text-gray-300">{{ $day->format('d') }}</span>
                        </span>
                    @endif
                @endfor
            </div>

            {{-- Next --}}
            @if($canGoNext)
                <a href="{{ route('manage.orders.index', array_merge(request()->except('date','page'), ['date' => $nextDate->format('Y-m-d')])) }}"
                   class="flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200
                          text-gray-400 hover:bg-orange-50 hover:border-orange-300 hover:text-orange-500 transition shrink-0">
                    ›
                </a>
            @else
                <span class="flex items-center justify-center w-9 h-9 rounded-xl border border-gray-100
                             text-gray-200 cursor-not-allowed shrink-0">›</span>
            @endif
        </div>

        {{-- Date label --}}
        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
            @if($selectedDate->isToday())
                <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                <span class="text-sm font-semibold text-orange-500">Hôm nay</span>
                <span class="text-gray-300">—</span>
            @endif
            <span class="text-sm text-gray-600">{{ $selectedDate->isoFormat('dddd, DD/MM/YYYY') }}</span>
            <span class="text-gray-300">·</span>
            <span class="text-xs text-gray-400">{{ $orders->total() }} order</span>
        </div>
    </div>

    {{-- ══ STATUS TABS + SEARCH ══ --}}
    @php
        $tabs = [
            'open'      => ['label' => 'Đang mở',       'dot' => 'bg-green-400'],
            'paid'      => ['label' => 'Đã thanh toán',  'dot' => 'bg-blue-400'],
            'cancelled' => ['label' => 'Đã huỷ',         'dot' => 'bg-gray-400'],
        ];
    @endphp
    <div class="bg-white rounded-2xl shadow mb-5 overflow-hidden">

        {{-- Tabs --}}
        <div class="flex overflow-x-auto border-b border-gray-100">
            @foreach($tabs as $val => $tab)
                @php $count = $statusCounts[$val] ?? 0; @endphp
                <a href="{{ route('manage.orders.index', array_merge(request()->except('status','page'), ['date' => $selectedDate->format('Y-m-d'), 'status' => $val])) }}"
                   class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                          {{ $activeStatus === $val
                              ? 'border-orange-500 text-orange-600'
                              : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                    <span class="w-2 h-2 rounded-full {{ $tab['dot'] }}"></span>
                    {{ $tab['label'] }}
                    @if($count > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-xs font-bold
                                     {{ $activeStatus === $val ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-500' }}">
                            {{ $count }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Search + filter bàn --}}
        <form method="GET" action="{{ route('manage.orders.index') }}"
              class="flex flex-wrap items-center gap-3 px-4 py-3">
            <input type="hidden" name="date"   value="{{ $selectedDate->format('Y-m-d') }}">
            <input type="hidden" name="status" value="{{ $activeStatus }}">

            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="🔍  Tìm tên bàn, tên khách..."
                   class="flex-1 min-w-40 px-4 py-2 rounded-xl border border-gray-200 focus:outline-none
                          focus:border-orange-400 bg-gray-50 text-sm">

            {{-- Filter theo bàn --}}
            <select name="table_id"
                    class="px-4 py-2 rounded-xl border border-gray-200 focus:outline-none
                           focus:border-orange-400 bg-gray-50 text-sm">
                <option value="">Tất cả bàn</option>
                @foreach($tables as $table)
                    <option value="{{ $table->id }}" {{ request('table_id') == $table->id ? 'selected' : '' }}>
                        {{ $table->name }} — {{ $table->area->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                    class="px-4 py-2 rounded-xl text-white text-sm font-semibold shrink-0"
                    style="background: #c8622a;">
                Lọc
            </button>

            @if(request()->hasAny(['search', 'table_id']))
                <a href="{{ route('manage.orders.index', ['date' => $selectedDate->format('Y-m-d'), 'status' => $activeStatus]) }}"
                   class="px-3 py-2 rounded-xl text-sm text-gray-500 bg-gray-100 hover:bg-gray-200 transition">
                    Xoá lọc
                </a>
            @endif
        </form>
    </div>

    {{-- ══ TABLE ══ --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead style="background: #2c1a0e;">
                <tr>
                    <th class="px-4 py-3 text-white font-medium">#</th>
                    <th class="px-4 py-3 text-white font-medium">Bàn</th>
                    <th class="px-4 py-3 text-white font-medium">Khách</th>
                    <th class="px-4 py-3 text-white font-medium">Số món</th>
                    <th class="px-4 py-3 text-white font-medium">Tổng tiền</th>
                    <th class="px-4 py-3 text-white font-medium">Nhân viên</th>
                    <th class="px-4 py-3 text-white font-medium">Thời gian</th>
                    <th class="px-4 py-3 text-white font-medium">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    @php
                        $guestName = $order->booking?->guest_name ?? '—';
                        $total     = $order->orderItems->sum(fn($i) => $i->quantity * $i->unit_price);
                    @endphp
                    <tr class="hover:bg-orange-50 transition">

                        <td class="px-4 py-3 text-gray-500">
                            {{ $orders->firstItem() + $loop->index }}
                        </td>

                        {{-- Bàn --}}
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $order->table->name }}</p>
                            <p class="text-xs text-gray-400">{{ $order->table->area->name }}</p>
                        </td>

                        {{-- Khách --}}
                        <td class="px-4 py-3">
                            @if($guestName !== '—')
                                @php
                                    $colors = [['#c8622a','#f5a623'],['#4b7cf3','#6c9fff'],['#6c47db','#9b6eff'],['#059669','#34d399']];
                                    $pair   = $colors[ord(mb_strtoupper(mb_substr($guestName,0,1))) % count($colors)];
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0"
                                         style="background: linear-gradient(135deg, {{ $pair[0] }}, {{ $pair[1] }})">
                                        {{ strtoupper(mb_substr($guestName, 0, 1)) }}
                                    </div>
                                    <span class="text-sm text-gray-700">{{ $guestName }}</span>
                                </div>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded bg-orange-100 text-orange-600 font-semibold">Walk-in</span>
                            @endif
                        </td>

                        {{-- Số món --}}
                        <td class="px-4 py-3 text-gray-600">
                            {{ $order->orderItems->count() }} món
                        </td>

                        {{-- Tổng tiền --}}
                        <td class="px-4 py-3 font-semibold text-gray-800">
                            {{ $total > 0 ? number_format($total) . 'đ' : '—' }}
                        </td>

                        {{-- Nhân viên --}}
                        <td class="px-4 py-3 text-gray-500">
                            {{ $order->staff->name ?? '—' }}
                        </td>

                        {{-- Thời gian --}}
                        <td class="px-4 py-3 text-gray-500">
                            {{ $order->created_at->format('H:i') }}
                            <span class="text-xs text-gray-400 block">{{ $order->created_at->format('d/m/Y') }}</span>
                        </td>

                        {{-- Hành động --}}
                        <td class="px-4 py-3">
                            <a href="{{ route('manage.orders.show', $order) }}"
                               class="px-3 py-1 rounded-lg text-xs font-medium transition
                                      {{ $activeStatus === 'open'
                                          ? 'bg-orange-50 text-orange-600 hover:bg-orange-100'
                                          : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                                {{ $activeStatus === 'open' ? 'Quản lý' : 'Chi tiết' }}
                            </a>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-gray-400">
                            <div class="text-4xl mb-2">🧾</div>
                            Không có order nào trong ngày
                            <strong class="text-orange-400">{{ $selectedDate->format('d/m/Y') }}</strong>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>

</div>
@endsection