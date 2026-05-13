@extends('layouts.manage')

@section('title', 'Quản lý đặt bàn')

@section('content')
<div>

    {{-- ── Top bar ── --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Quản lý đặt bàn</h1>
        <a href="{{ route('manage.bookings.create') }}"
           class="px-4 py-2 rounded-xl text-white text-sm font-semibold"
           style="background: linear-gradient(135deg, #c8622a, #f5a623);">
            + Tạo Walk-in
        </a>
    </div>

    {{-- ══ DAY NAVIGATOR ══ --}}
    <div class="bg-white rounded-2xl shadow p-4 mb-5">

        {{-- Week pills + prev/next --}}
        <div class="flex items-center gap-2">

            {{-- Prev --}}
            @if($canGoPrev)
                <a href="{{ route('manage.bookings.index', array_merge(request()->except('date','page'), ['date' => $prevDate->format('Y-m-d')])) }}"
                   class="flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200
                          text-gray-400 hover:bg-orange-50 hover:border-orange-300 hover:text-orange-500 transition shrink-0">
                    ‹
                </a>
            @else
                <span class="flex items-center justify-center w-9 h-9 rounded-xl border border-gray-100
                             text-gray-200 cursor-not-allowed shrink-0">‹</span>
            @endif

            {{-- 7 day pills --}}
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
                        <a href="{{ route('manage.bookings.index', array_merge(request()->except('date','page'), ['date' => $day->format('Y-m-d')])) }}"
                           class="flex flex-col items-center px-3 py-2 rounded-xl min-w-[48px] flex-1 transition text-center
                                  {{ $isActive
                                      ? 'text-white'
                                      : 'text-gray-500 hover:bg-orange-50 hover:text-orange-500' }}"
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
                        <span class="flex flex-col items-center px-3 py-2 rounded-xl min-w-[48px] flex-1 text-center opacity-25 cursor-not-allowed">
                            <span class="text-[10px] font-semibold uppercase tracking-wide text-gray-300">{{ $vnDays[$day->dayOfWeek] }}</span>
                            <span class="text-sm font-bold mt-0.5 text-gray-300">{{ $day->format('d') }}</span>
                        </span>
                    @endif
                @endfor
            </div>

            {{-- Next --}}
            @if($canGoNext)
                <a href="{{ route('manage.bookings.index', array_merge(request()->except('date','page'), ['date' => $nextDate->format('Y-m-d')])) }}"
                   class="flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200
                          text-gray-400 hover:bg-orange-50 hover:border-orange-300 hover:text-orange-500 transition shrink-0">
                    ›
                </a>
            @else
                <span class="flex items-center justify-center w-9 h-9 rounded-xl border border-gray-100
                             text-gray-200 cursor-not-allowed shrink-0">›</span>
            @endif
        </div>

        {{-- Date label + status note --}}
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100 flex-wrap gap-2">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                @if($isToday)
                    <span class="w-2 h-2 rounded-full bg-orange-400 inline-block"></span>
                    <span class="font-semibold text-orange-500">Hôm nay</span>
                    <span class="text-gray-400">—</span>
                @endif
                <span>{{ $selectedDate->isoFormat('dddd, DD/MM/YYYY') }}</span>
                <span class="text-gray-300">·</span>
                <span class="text-gray-400 text-xs">{{ $bookings->total() }} đơn</span>
            </div>

            {{-- Rule badge --}}
            @if($isToday)
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                    ✓ Xác nhận · Hoàn tất · Huỷ
                </span>
            @elseif($isFuture)
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-600">
                    📅 Tương lai — chỉ có thể Huỷ
                </span>
            @else
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                    🔒 Ngày đã qua — chỉ xem
                </span>
            @endif
        </div>
    </div>

    {{-- ══ STATUS TABS ══ --}}
    @php
        $tabs = [
            'pending'   => ['label' => 'Chờ xác nhận', 'dot' => 'bg-yellow-400'],
            'confirmed' => ['label' => 'Đã xác nhận',  'dot' => 'bg-green-400'],
            'completed' => ['label' => 'Hoàn tất',     'dot' => 'bg-blue-400'],
            'cancelled' => ['label' => 'Đã huỷ',       'dot' => 'bg-gray-400'],
            'no_show'   => ['label' => 'Không đến',    'dot' => 'bg-red-400'],
        ];
    @endphp
    <div class="bg-white rounded-2xl shadow mb-5 overflow-hidden">
        <div class="flex overflow-x-auto border-b border-gray-100">
            @foreach($tabs as $val => $tab)
                @php $count = $statusCounts[$val] ?? 0; @endphp
                <a href="{{ route('manage.bookings.index', array_merge(request()->except('status','page'), ['date' => $selectedDate->format('Y-m-d'), 'status' => $val])) }}"
                   class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                          {{ $activeStatus === $val
                              ? 'border-orange-500 text-orange-600'
                              : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                    <span class="w-2 h-2 rounded-full shrink-0 {{ $tab['dot'] }}"></span>
                    {{ $tab['label'] }}
                    @if($count > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-xs font-bold
                                     {{ $activeStatus === $val
                                         ? 'bg-orange-100 text-orange-600'
                                         : 'bg-gray-100 text-gray-500' }}">
                            {{ $count }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Search bên trong tab bar --}}
        <form method="GET" action="{{ route('manage.bookings.index') }}"
              class="flex items-center gap-3 px-4 py-3">
            <input type="hidden" name="date"   value="{{ $selectedDate->format('Y-m-d') }}">
            <input type="hidden" name="status" value="{{ $activeStatus }}">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="🔍  Tìm tên khách, SĐT..."
                   class="flex-1 px-4 py-2 rounded-xl border border-gray-200 focus:outline-none
                          focus:border-orange-400 bg-gray-50 text-sm">
            <button type="submit"
                    class="px-4 py-2 rounded-xl text-white text-sm font-semibold shrink-0"
                    style="background: #c8622a;">
                Tìm
            </button>
            @if(request('search'))
                <a href="{{ route('manage.bookings.index', ['date' => $selectedDate->format('Y-m-d'), 'status' => $activeStatus]) }}"
                   class="px-3 py-2 rounded-xl text-sm text-gray-500 bg-gray-100 hover:bg-gray-200 transition shrink-0">
                    ✕
                </a>
            @endif
        </form>
    </div>

    {{-- ── Table ── --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead style="background: #2c1a0e;">
                <tr>
                    <th class="px-4 py-3 text-white font-medium">#</th>
                    <th class="px-4 py-3 text-white font-medium">Khách hàng</th>
                    <th class="px-4 py-3 text-white font-medium">Bàn / Khu vực</th>
                    <th class="px-4 py-3 text-white font-medium">Giờ đặt</th>
                    <th class="px-4 py-3 text-white font-medium">Số người</th>
                    <th class="px-4 py-3 text-white font-medium">Trạng thái</th>
                    <th class="px-4 py-3 text-white font-medium">Nhân viên</th>
                    <th class="px-4 py-3 text-white font-medium">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bookings as $booking)
                    @php
                        $displayName = $booking->guest_name
                            ?: ($booking->user->name ?? 'Khách vãng lai');

                        // Avatar gradient — hash từ ký tự đầu
                        $avatarColors = [
                            ['#c8622a','#f5a623'],
                            ['#4b7cf3','#6c9fff'],
                            ['#6c47db','#9b6eff'],
                            ['#0ea5e9','#38bdf8'],
                            ['#059669','#34d399'],
                            ['#db2777','#f472b6'],
                        ];
                        $pair = $avatarColors[ord(mb_strtoupper(mb_substr($displayName,0,1))) % count($avatarColors)];

                        $statusConfig = [
                            'pending'   => ['label' => 'Chờ xác nhận', 'class' => 'bg-yellow-100 text-yellow-700'],
                            'confirmed' => ['label' => 'Đã xác nhận',  'class' => 'bg-green-100 text-green-700'],
                            'completed' => ['label' => 'Hoàn tất',     'class' => 'bg-blue-100 text-blue-700'],
                            'cancelled' => ['label' => 'Đã huỷ',       'class' => 'bg-gray-100 text-gray-500'],
                            'no_show'   => ['label' => 'Không đến',    'class' => 'bg-red-100 text-red-700'],
                        ];
                        $cfg = $statusConfig[$booking->status] ?? $statusConfig['pending'];
                    @endphp
                    <tr class="hover:bg-orange-50 transition">

                        {{-- # --}}
                        <td class="px-4 py-3 text-gray-500">
                            {{ $bookings->firstItem() + $loop->index }}
                        </td>

                        {{-- Khách --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center
                                            text-white text-sm font-bold shrink-0"
                                     style="background: linear-gradient(135deg, {{ $pair[0] }}, {{ $pair[1] }})">
                                    {{ strtoupper(mb_substr($displayName, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">
                                        {{ $displayName }}
                                        @if(!$booking->user_id)
                                            <span class="ml-1 text-xs px-1.5 py-0.5 rounded
                                                         bg-orange-100 text-orange-600 font-semibold">
                                                Walk-in
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $booking->guest_phone ?: '—' }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Bàn --}}
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $booking->table->name }}</p>
                            <p class="text-xs text-gray-400">{{ $booking->table->area->name }}</p>
                        </td>

                        {{-- Giờ --}}
                        <td class="px-4 py-3 text-gray-600">
                            <p>{{ $booking->start_time->format('H:i') }} → {{ $booking->end_time->format('H:i') }}</p>
                            @if($booking->note)
                                <p class="text-xs text-gray-400 italic truncate max-w-[130px]"
                                   title="{{ $booking->note }}">
                                    📝 {{ $booking->note }}
                                </p>
                            @endif
                        </td>

                        {{-- Số người --}}
                        <td class="px-4 py-3 text-gray-600">
                            {{ $booking->guest_count }} người
                        </td>

                        {{-- Trạng thái --}}
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cfg['class'] }}">
                                {{ $cfg['label'] }}
                            </span>
                        </td>

                        {{-- Nhân viên --}}
                        <td class="px-4 py-3 text-gray-500 text-sm">
                            {{ $booking->staff->name ?? '—' }}
                        </td>

                        {{-- Hành động --}}
                        <td class="px-4 py-3">
                            <div class="flex gap-2 flex-wrap">

                                {{-- Chi tiết — luôn hiện --}}
                                <a href="{{ route('manage.bookings.show', $booking) }}"
                                   class="px-3 py-1 rounded-lg text-xs font-medium
                                          bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                                    Chi tiết
                                </a>

                                @if($isToday)
                                    {{-- Hôm nay: confirm + complete + cancel --}}
                                    @if($booking->status === 'pending')
                                        <form action="{{ route('manage.bookings.confirm', $booking) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="px-3 py-1 rounded-lg text-xs font-medium
                                                           bg-green-50 text-green-600 hover:bg-green-100 transition">
                                                Xác nhận
                                            </button>
                                        </form>
                                    @endif

                                    @if($booking->status === 'confirmed')
                                        <form action="{{ route('manage.bookings.complete', $booking) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="px-3 py-1 rounded-lg text-xs font-medium
                                                           bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                                Hoàn tất
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($booking->status, ['pending', 'confirmed']))
                                        <form action="{{ route('manage.bookings.cancel', $booking) }}" method="POST"
                                              onsubmit="return confirm('Huỷ booking này?')">
                                            @csrf
                                            <button type="submit"
                                                    class="px-3 py-1 rounded-lg text-xs font-medium
                                                           bg-red-50 text-red-600 hover:bg-red-100 transition">
                                                Huỷ
                                            </button>
                                        </form>
                                    @endif

                                @elseif($isFuture)
                                    {{-- Tương lai: chỉ cancel pending/confirmed --}}
                                    @if(in_array($booking->status, ['pending', 'confirmed']))
                                        <form action="{{ route('manage.bookings.cancel', $booking) }}" method="POST"
                                              onsubmit="return confirm('Huỷ booking này?')">
                                            @csrf
                                            <button type="submit"
                                                    class="px-3 py-1 rounded-lg text-xs font-medium
                                                           bg-red-50 text-red-600 hover:bg-red-100 transition">
                                                Huỷ
                                            </button>
                                        </form>
                                    @endif

                                @else
                                    {{-- Quá khứ: chỉ xem --}}
                                    <span class="text-xs text-gray-300 italic">Chỉ xem</span>

                                @endif
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-gray-400">
                            <div class="text-4xl mb-2">🍽️</div>
                            Không có đặt bàn nào trong ngày
                            <strong class="text-orange-400">{{ $selectedDate->format('d/m/Y') }}</strong>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>

</div>
@endsection