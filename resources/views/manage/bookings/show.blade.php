@extends('layouts.manage')

@section('title', 'Chi tiết booking')

@section('content')
<div class="max-w-3xl">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('manage.bookings.index') }}"
           class="text-gray-400 hover:text-gray-600 transition">← Quay lại</a>
        <h1 class="text-2xl font-bold text-gray-800">Chi tiết booking #{{ $booking->id }}</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Thông tin booking --}}
        <div class="bg-white rounded-2xl shadow p-6 space-y-4">
            <h2 class="font-semibold text-gray-700 text-lg border-b pb-2">Thông tin đặt bàn</h2>

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Khách hàng</p>
                <p class="font-medium text-gray-800">{{ $booking->guest_name }}</p>
                <p class="text-sm text-gray-500">{{ $booking->guest_phone ?? '—' }}</p>
                @if(!$booking->user_id)
                    <span class="text-xs px-2 py-0.5 rounded bg-orange-100 text-orange-600">
                        Walk-in
                    </span>
                @else
                    <span class="text-xs px-2 py-0.5 rounded bg-blue-100 text-blue-600">
                        Đặt online
                    </span>
                @endif
            </div>

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Bàn</p>
                <p class="font-medium text-gray-800">{{ $booking->table->name }}</p>
                <p class="text-sm text-gray-500">📍 {{ $booking->table->area->name }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Thời gian</p>
                <p class="font-medium text-gray-800">
                    {{ $booking->start_time->format('H:i') }}
                    →
                    {{ $booking->end_time->format('H:i') }}
                </p>
                <p class="text-sm text-gray-500">{{ $booking->start_time->format('d/m/Y') }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Số người</p>
                <p class="font-medium text-gray-800">{{ $booking->guest_count }} người</p>
            </div>

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Ghi chú</p>
                <p class="text-gray-700">{{ $booking->note ?? '—' }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Trạng thái</p>
                @php
                    $statusConfig = [
                        'pending'   => ['label' => 'Chờ xác nhận', 'class' => 'bg-yellow-100 text-yellow-700'],
                        'confirmed' => ['label' => 'Đã xác nhận',  'class' => 'bg-green-100 text-green-700'],
                        'completed' => ['label' => 'Hoàn tất',     'class' => 'bg-blue-100 text-blue-700'],
                        'cancelled' => ['label' => 'Đã huỷ',       'class' => 'bg-gray-100 text-gray-500'],
                        'no_show'   => ['label' => 'Không đến',    'class' => 'bg-red-100 text-red-700'],
                    ];
                    $config = $statusConfig[$booking->status];
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $config['class'] }}">
                    {{ $config['label'] }}
                </span>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2 pt-2 border-t">
                @if($booking->status === 'pending')
                    <form action="{{ route('manage.bookings.confirm', $booking) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 rounded-xl text-sm font-medium
                                       bg-green-50 text-green-600 hover:bg-green-100 transition">
                            ✅ Xác nhận
                        </button>
                    </form>
                @endif

                @if($booking->status === 'confirmed')
                    <form action="{{ route('manage.bookings.complete', $booking) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 rounded-xl text-sm font-medium
                                       bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                            🏁 Hoàn tất
                        </button>
                    </form>
                @endif

                @if(in_array($booking->status, ['pending', 'confirmed']))
                    <form action="{{ route('manage.bookings.cancel', $booking) }}" method="POST"
                          onsubmit="return confirm('Huỷ booking này?')">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 rounded-xl text-sm font-medium
                                       bg-red-50 text-red-600 hover:bg-red-100 transition">
                            ❌ Huỷ
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Lịch sử hành động --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="font-semibold text-gray-700 text-lg border-b pb-2 mb-4">
                Lịch sử hành động
            </h2>

            @if($booking->logs->isEmpty())
                <p class="text-gray-400 text-sm text-center py-4">Chưa có hành động nào</p>
            @else
                <div class="space-y-4">
                    @foreach($booking->logs as $log)
                        @php
                            $actionConfig = [
                                'confirmed' => ['icon' => '✅', 'class' => 'text-green-600'],
                                'completed' => ['icon' => '🏁', 'class' => 'text-blue-600'],
                                'cancelled' => ['icon' => '❌', 'class' => 'text-red-600'],
                                'no_show'   => ['icon' => '⏰', 'class' => 'text-orange-600'],
                            ];
                            $actionCfg = $actionConfig[$log->action] ?? ['icon' => '•', 'class' => 'text-gray-600'];
                        @endphp
                        <div class="flex items-start gap-3">
                            <span class="text-lg">{{ $actionCfg['icon'] }}</span>
                            <div>
                                <p class="text-sm font-medium {{ $actionCfg['class'] }}">
                                    {{ ucfirst($log->action) }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    👤 {{ $log->staff->name }}
                                </p>
                                @if($log->note)
                                    <p class="text-xs text-gray-400 italic">
                                        {{ $log->note }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-400">
                                    🕐 {{ $log->created_at->format('H:i d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>
@endsection