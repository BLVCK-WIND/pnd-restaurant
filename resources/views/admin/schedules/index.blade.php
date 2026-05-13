@extends('layouts.admin')

@section('title', 'Phân ca làm việc')

@section('content')
<div>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Phân ca làm việc</h1>
        @if($isPastWeek)
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                📋 Tuần đã qua — chỉ xem
            </span>
        @endif
    </div>

    {{-- Điều hướng tuần + Filter --}}
    <div class="bg-white rounded-2xl shadow p-4 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">

            {{-- Điều hướng tuần --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.schedules.index', array_merge(request()->query(), ['week' => $weekStart->copy()->subWeek()->format('Y-m-d')])) }}"
                   class="px-3 py-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition text-sm">
                    ← Tuần trước
                </a>

                <div class="text-center">
                    <p class="font-semibold text-gray-700 text-sm">
                        {{ $weekStart->format('d/m') }} — {{ $weekEnd->format('d/m/Y') }}
                    </p>
                    @if($isPastWeek)
                        <p class="text-xs text-gray-400">Tuần đã qua</p>
                    @elseif($weekStart->isCurrentWeek())
                        <p class="text-xs text-orange-500">Tuần hiện tại</p>
                    @else
                        <p class="text-xs text-blue-500">Tuần sắp tới</p>
                    @endif
                </div>

                <a href="{{ route('admin.schedules.index', array_merge(request()->query(), ['week' => $weekStart->copy()->addWeek()->format('Y-m-d')])) }}"
                   class="px-3 py-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition text-sm">
                    Tuần sau →
                </a>

                @if(!$weekStart->isCurrentWeek())
                    <a href="{{ route('admin.schedules.index') }}"
                       class="px-3 py-2 rounded-xl text-white text-sm transition"
                       style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                        Tuần này
                    </a>
                @endif
            </div>

            {{-- Filter tên nhân viên --}}
            <form method="GET"
                  action="{{ route('admin.schedules.index', ['week' => $weekStart->format('Y-m-d')]) }}"
                  class="flex gap-3">
                <input type="hidden" name="week" value="{{ $weekStart->format('Y-m-d') }}">
                <input type="text" name="search"
                       value="{{ request('search') }}"
                       placeholder="Tìm nhân viên..."
                       class="px-4 py-2 rounded-xl border border-gray-200 text-sm
                              focus:outline-none focus:border-orange-400 bg-gray-50">
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-white text-sm"
                        style="background: #c8622a;">
                    Tìm
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.schedules.index', ['week' => $weekStart->format('Y-m-d')]) }}"
                       class="px-4 py-2 rounded-xl text-sm bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                        Xoá
                    </a>
                @endif
            </form>

        </div>
    </div>

    {{-- Bảng lịch --}}
    <div class="bg-white rounded-2xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #2c1a0e;">
                <tr>
                    {{-- Cột nhân viên --}}
                    <th class="px-4 py-3 text-white font-medium text-left w-48">
                        Nhân viên
                    </th>
                    {{-- Cột các ngày --}}
                    @foreach($days as $day)
                        <th class="px-2 py-3 text-white font-medium text-center min-w-36">
                            <p>{{ $day->locale('vi')->dayName }}</p>
                            <p class="text-orange-300 text-xs font-normal">
                                {{ $day->format('d/m') }}
                            </p>
                            @if($day->isToday())
                                <span class="text-xs bg-orange-400 text-white px-1.5 py-0.5 rounded-full">
                                    Hôm nay
                                </span>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($staffs as $staff)
                    <tr class="hover:bg-orange-50 transition">

                        {{-- Tên nhân viên + tổng ca --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center
                                            text-white text-sm font-bold shrink-0"
                                     style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                                    {{ strtoupper(substr($staff->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $staff->name }}</p>
                                    <p class="text-xs text-gray-400">
                                        <span id="total-{{ $staff->id }}">
                                            {{ $schedules->get($staff->id)?->flatten()->count() ?? 0 }}
                                        </span> ca/tuần
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Ô mỗi ngày --}}
                        @foreach($days as $day)
                            @php
                                $dateStr      = $day->format('Y-m-d');
                                $daySchedules = $schedules->get($staff->id)?->get($dateStr) ?? collect();
                                $assignedShiftIds = $daySchedules->pluck('shift_id')->toArray();
                            @endphp
                            <td class="px-2 py-2 text-center">

                                {{-- Hiển thị ca đã phân --}}
                                <div id="display-{{ $staff->id }}-{{ $dateStr }}"
                                     class="flex flex-wrap gap-1 justify-center mb-1 min-h-6">
                                    @foreach($daySchedules as $schedule)
                                        @if ($schedule->shift->name === 'Ca sáng')
                                           <span class="px-2 py-0.5 rounded-full text-xs
                                                        bg-yellow-200 text-yellow-700 font-semibold">
                                                {{ $schedule->shift->name }}
                                            </span>
                                        @elseif($schedule->shift->name === 'Ca chiều')
                                            <span class="px-2 py-0.5 rounded-full text-xs
                                                        bg-blue-200 text-blue-700 font-semibold">
                                                {{ $schedule->shift->name }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs
                                                        bg-purple-200 text-purple-700 font-semibold">
                                                {{ $schedule->shift->name }}
                                            </span>
                                        @endif
                                        
                                    @endforeach
                                </div>

                                @if(!$isPastWeek)
                                    {{-- Nút mở popup --}}
                                    <button
                                        onclick="openPopup('{{ $staff->id }}', '{{ $dateStr }}', {{ json_encode($assignedShiftIds) }})"
                                        class="px-2 py-1 rounded-lg text-xs transition
                                               {{ $daySchedules->isEmpty()
                                                   ? 'bg-gray-100 text-gray-400 hover:bg-orange-100 hover:text-orange-600'
                                                   : 'bg-orange-50 text-orange-500 hover:bg-orange-100' }}">
                                        {{ $daySchedules->isEmpty() ? '+ Phân ca' : '✏️ Sửa' }}
                                    </button>
                                @endif

                            </td>
                        @endforeach

                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($days) + 1 }}"
                            class="px-4 py-8 text-center text-gray-400">
                            Không có nhân viên nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- Popup phân ca --}}
<div id="popup-overlay"
     class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden items-center justify-center"
     onclick="closePopup()">

    <div class="bg-white rounded-2xl shadow-xl p-6 w-80"
         onclick="event.stopPropagation()">

        <h3 class="font-semibold text-gray-800 text-lg mb-1">Phân ca làm việc</h3>
        <p id="popup-subtitle" class="text-sm text-gray-400 mb-4"></p>

        {{-- Danh sách ca dạng checkbox --}}
        <div class="space-y-3 mb-5">
            @foreach($shifts as $shift)
                <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl
                              hover:bg-orange-50 transition border border-gray-100">
                    <input type="checkbox"
                           value="{{ $shift->id }}"
                           class="shift-checkbox w-4 h-4 accent-orange-500">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $shift->name }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $shift->start_time }} — {{ $shift->end_time }}
                        </p>
                    </div>
                </label>
            @endforeach
        </div>

        {{-- Buttons --}}
        <div class="flex gap-3">
            <button onclick="saveSchedule()"
                    class="flex-1 py-2.5 rounded-xl text-white text-sm font-semibold transition"
                    style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                ✅ Lưu
            </button>
            <button onclick="closePopup()"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold
                           bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Huỷ
            </button>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';

    // Dữ liệu tên ca để hiển thị trong UI
    const shiftNames = {
        @foreach($shifts as $shift)
            {{ $shift->id }}: '{{ $shift->name }}',
        @endforeach
    };

    let currentUserId   = null;
    let currentDate     = null;
    let originalShiftIds = [];

    // Mở popup
    function openPopup(userId, date, assignedShiftIds) {
        currentUserId    = userId;
        currentDate      = date;
        originalShiftIds = assignedShiftIds;

        // Set subtitle
        document.getElementById('popup-subtitle').textContent =
            `Nhân viên #${userId} — ${date}`;

        // Set trạng thái checkbox
        document.querySelectorAll('.shift-checkbox').forEach(cb => {
            cb.checked = assignedShiftIds.includes(parseInt(cb.value));
        });

        // Hiện popup
        document.getElementById('popup-overlay').classList.remove('hidden');
        document.getElementById('popup-overlay').classList.add('flex');
    }

    // Đóng popup
    function closePopup() {
        document.getElementById('popup-overlay').classList.add('hidden');
        document.getElementById('popup-overlay').classList.remove('flex');
        currentUserId    = null;
        currentDate      = null;
        originalShiftIds = [];
    }

    // Lưu lịch
    async function saveSchedule() {
        const checkedShiftIds = [...document.querySelectorAll('.shift-checkbox:checked')]
            .map(cb => parseInt(cb.value));

        // Tìm ca cần thêm và ca cần xoá
        const toAdd    = checkedShiftIds.filter(id => !originalShiftIds.includes(id));
        const toRemove = originalShiftIds.filter(id => !checkedShiftIds.includes(id));

        // Gọi toggle cho từng ca thay đổi
        const promises = [
            ...toAdd.map(shiftId => callToggle(shiftId)),
            ...toRemove.map(shiftId => callToggle(shiftId)),
        ];

        try {
            const results = await Promise.all(promises);
            const lastResult = results[results.length - 1];

            // Cập nhật tổng ca
            if (lastResult) {
                document.getElementById(`total-${currentUserId}`).textContent =
                    lastResult.total_shifts;
            }

            // Cập nhật display ca trong ô
            updateDisplay(checkedShiftIds);

            closePopup();

        } catch (err) {
            alert('Có lỗi xảy ra, vui lòng thử lại');
            console.error(err);
        }
    }

    // Gọi API toggle 1 ca
    async function callToggle(shiftId) {
        const res = await fetch('{{ route('admin.schedules.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                user_id:   currentUserId,
                shift_id:  shiftId,
                work_date: currentDate,
            }),
        });

        if (!res.ok) {
            const data = await res.json();
            throw new Error(data.error ?? 'Lỗi server');
        }

        return res.json();
    }

    // Cập nhật hiển thị ca trong ô bảng
    function updateDisplay(shiftIds) {
        const display = document.getElementById(`display-${currentUserId}-${currentDate}`);

        if (shiftIds.length === 0) {
            display.innerHTML = '';
        } else {
            display.innerHTML = shiftIds.map(id => `
                <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">
                    ${shiftNames[id]}
                </span>
            `).join('');
        }

        // Cập nhật nút phân ca / sửa
        const btn = display.nextElementSibling;
        if (btn) {
            if (shiftIds.length === 0) {
                btn.textContent = '+ Phân ca';
                btn.className = btn.className
                    .replace('bg-orange-50 text-orange-500 hover:bg-orange-100',
                             'bg-gray-100 text-gray-400 hover:bg-orange-100 hover:text-orange-600');
            } else {
                btn.textContent = '✏️ Sửa';
                btn.className = btn.className
                    .replace('bg-gray-100 text-gray-400 hover:bg-orange-100 hover:text-orange-600',
                             'bg-orange-50 text-orange-500 hover:bg-orange-100');
            }
            // Cập nhật onclick với shiftIds mới
            btn.setAttribute('onclick',
                `openPopup('${currentUserId}', '${currentDate}', ${JSON.stringify(shiftIds)})`);
        }
    }
</script>
@endpush