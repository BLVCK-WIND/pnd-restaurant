@extends('layouts.staff')

@section('title', 'Lịch làm việc')

@section('content')
<div>

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Lịch làm việc</h1>

    {{-- Điều hướng tuần --}}
    <div class="bg-white rounded-2xl shadow p-4 mb-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('staff.schedules.index', ['week' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}"
               class="px-3 py-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition text-sm">
                ← Tuần trước
            </a>
            <div class="text-center">
                <p class="font-semibold text-gray-700">
                    {{ $weekStart->format('d/m') }} — {{ $weekEnd->format('d/m/Y') }}
                </p>
                @if($weekStart->isCurrentWeek())
                    <p class="text-xs text-orange-500">Tuần hiện tại</p>
                @elseif($weekEnd->isPast())
                    <p class="text-xs text-gray-400">Tuần đã qua</p>
                @else
                    <p class="text-xs text-blue-500">Tuần sắp tới</p>
                @endif
            </div>
            <a href="{{ route('staff.schedules.index', ['week' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}"
               class="px-3 py-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition text-sm">
                Tuần sau →
            </a>
        </div>
    </div>

    @php
        /**
         * Xác định slot ca theo start_time:
         *   sáng  : 05:00 – 11:59  → slot 0
         *   chiều : 12:00 – 16:59  → slot 1
         *   tối   : 17:00 – 23:59  → slot 2
         */
        function shiftSlot(string $startTime): int {
            $h = (int) substr($startTime, 0, 2);
            if ($h >= 5  && $h < 12) return 0;
            if ($h >= 12 && $h < 17) return 1;
            return 2;
        }

        $slots = [
            0 => ['label' => 'Sáng',  'bg' => 'bg-yellow-400', 'bgLight' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
            1 => ['label' => 'Chiều', 'bg' => 'bg-blue-400',   'bgLight' => 'bg-blue-100',   'text' => 'text-blue-700'],
            2 => ['label' => 'Tối',   'bg' => 'bg-purple-400', 'bgLight' => 'bg-purple-100', 'text' => 'text-purple-700'],
        ];
    @endphp

    {{-- Chú thích --}}
    <div class="bg-white rounded-2xl shadow p-4 mb-6 flex flex-wrap items-center gap-4">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Chú thích:</span>
        @foreach($slots as $slot)
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full {{ $slot['bg'] }}"></span>
                <span class="text-xs text-gray-600 font-medium">Ca {{ $slot['label'] }}</span>
            </div>
        @endforeach
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-gray-200"></span>
            <span class="text-xs text-gray-400">Không có ca</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ===== LỊCH CỦA TÔI ===== --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="font-semibold text-gray-700 text-lg border-b pb-2 mb-4">
                📅 Lịch của tôi
            </h2>

            @php
                $mySchedules   = $schedules->get(auth()->id()) ?? collect();
                $myTotalShifts = $mySchedules->flatten()->count();
            @endphp

            @if($mySchedules->isEmpty())
                <div class="text-center text-gray-400 py-8">
                    <p class="text-4xl mb-2">📭</p>
                    <p>Tuần này chưa có lịch làm việc</p>
                </div>
            @else
                <div class="mb-4 px-3 py-2 rounded-xl bg-orange-50 text-orange-700 text-sm font-medium inline-block">
                    🗓 Tổng: {{ $myTotalShifts }} ca trong tuần này
                </div>

                <div class="space-y-3">
                    @foreach($days as $day)
                        @php
                            $dateStr      = $day->format('Y-m-d');
                            $daySchedules = $mySchedules->get($dateStr) ?? collect();
                            $isToday      = $day->isToday();

                            // Map từng schedule vào đúng slot 0/1/2
                            $slotMap = [0 => null, 1 => null, 2 => null];
                            foreach ($daySchedules as $s) {
                                $slotMap[shiftSlot($s->shift->start_time)] = $s;
                            }
                        @endphp
                        <div class="flex items-center gap-3
                                    {{ $isToday ? 'bg-orange-50 rounded-xl px-3 py-2 -mx-3' : '' }}">

                            {{-- Cột ngày --}}
                            <div class="w-20 shrink-0 text-sm {{ $isToday ? 'font-bold text-orange-600' : 'text-gray-500' }}">
                                <p>{{ $day->locale('vi')->dayName }}</p>
                                <p class="text-xs">{{ $day->format('d/m') }}</p>
                                @if($isToday)
                                    <span class="text-xs bg-orange-400 text-white px-1.5 py-0.5 rounded-full">Hôm nay</span>
                                @endif
                            </div>

                            {{-- 3 slot cố định: Sáng / Chiều / Tối --}}
                            <div class="flex-1 flex gap-2">
                                @foreach($slots as $slotIndex => $slotCfg)
                                    @if($slotMap[$slotIndex])
                                        @php $s = $slotMap[$slotIndex]; @endphp
                                        <span class="flex-1 px-2 py-1 rounded-full text-xs font-medium text-center
                                                     {{ $slotCfg['bgLight'] }} {{ $slotCfg['text'] }}">
                                            {{ substr($s->shift->start_time, 0, 5) }}–{{ substr($s->shift->end_time, 0, 5) }}
                                        </span>
                                    @else
                                        <span class="flex-1 px-2 py-1 rounded-full text-xs text-center
                                                     bg-gray-50 border border-dashed border-gray-200 text-gray-200">
                                            —
                                        </span>
                                    @endif
                                @endforeach
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===== LỊCH CỦA MỌI NGƯỜI ===== --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="font-semibold text-gray-700 text-lg border-b pb-2 mb-4">
                👥 Lịch của mọi người
            </h2>

            {{-- Header thứ + ngày --}}
            <div class="flex items-center gap-1 mb-3">
                <div class="w-28 shrink-0"></div>
                @foreach($days as $day)
                    <div class="flex-1 text-center">
                        <p class="text-xs font-semibold {{ $day->isToday() ? 'text-orange-500' : 'text-gray-500' }}">
                            {{ $day->locale('vi')->shortDayName }}
                        </p>
                        <p class="text-xs {{ $day->isToday() ? 'text-orange-400' : 'text-gray-400' }}">
                            {{ $day->format('d') }}
                        </p>
                    </div>
                @endforeach
            </div>

            <div class="space-y-3 max-h-[520px] overflow-y-auto pr-1">
                @foreach($staffs as $staff)
                    @php
                        $staffSchedules = $schedules->get($staff->id) ?? collect();
                        $totalShifts    = $staffSchedules->flatten()->count();
                        $isMe           = $staff->id === auth()->id();
                    @endphp

                    {{-- 1 row: tên bên trái + thanh 7 ngày bên phải --}}
                    <div class="flex items-center gap-1
                                {{ $isMe ? 'bg-orange-50 rounded-xl px-2 py-1.5 -mx-2' : 'py-1' }}">

                        {{-- Tên nhân viên — w-28 căn với header --}}
                        <div class="w-28 shrink-0 flex items-center gap-1.5">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center
                                        text-white text-xs font-bold shrink-0"
                                 style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                                {{ strtoupper(substr($staff->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-gray-700 truncate">{{ $staff->name }}</p>
                                @if($isMe)
                                    <p class="text-orange-400 text-xs leading-none">(bạn)</p>
                                @endif
                                <p class="text-xs {{ $totalShifts > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $totalShifts }} ca
                                </p>
                            </div>
                        </div>

                        {{-- 7 cột ngày, mỗi cột có 3 thanh Sáng/Chiều/Tối --}}
                        @foreach($days as $day)
                            @php
                                $dateStr      = $day->format('Y-m-d');
                                $daySchedules = $staffSchedules->get($dateStr) ?? collect();
                                $isToday      = $day->isToday();
                            @endphp
                            <div class="flex-1 flex flex-col gap-0.5
                                        {{ $isToday ? 'rounded ring-1 ring-orange-200' : '' }}">
                                @foreach($slots as $slotIndex => $slotCfg)
                                    @php
                                        $hasShift   = false;
                                        $shiftLabel = '';
                                        foreach ($daySchedules as $s) {
                                            if (shiftSlot($s->shift->start_time) === $slotIndex) {
                                                $hasShift   = true;
                                                $shiftLabel = substr($s->shift->start_time, 0, 5)
                                                            . '–' . substr($s->shift->end_time, 0, 5);
                                                break;
                                            }
                                        }
                                    @endphp
                                    <div class="h-2.5 rounded-sm
                                                {{ $hasShift ? $slotCfg['bg'] : 'bg-gray-100' }}"
                                         title="{{ $hasShift
                                                    ? 'Ca ' . $slotCfg['label'] . ': ' . $shiftLabel
                                                    : 'Không có ca ' . $slotCfg['label'] }}">
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                    </div>
                @endforeach
            </div>

        </div>

    </div>

</div>
@endsection