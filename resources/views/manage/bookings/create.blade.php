@extends('layouts.manage')

@section('title', 'Tạo Walk-in')

@section('content')
<div class="max-w-2xl">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('manage.bookings.index') }}"
           class="text-gray-400 hover:text-gray-600 transition">← Quay lại</a>
        <h1 class="text-2xl font-bold text-gray-800">Tạo đơn Walk-in</h1>
    </div>

    <div class="bg-white rounded-2xl shadow p-6">

        {{-- Thông báo thời gian --}}
        <div class="bg-orange-50 rounded-xl p-4 mb-6">
            <p class="text-sm text-orange-700 font-medium">
                🕐 Thời gian walk-in:
                <strong>{{ \Carbon\Carbon::parse($start_time)->format('H:i d/m/Y') }}</strong>
                →
                <strong>{{ \Carbon\Carbon::parse($end_time)->format('H:i d/m/Y') }}</strong>
            </p>
            <p class="text-xs text-orange-500 mt-1">
                Thời gian tối đa 3 tiếng kể từ lúc tạo đơn
            </p>
        </div>

        <form action="{{ route('manage.bookings.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Hidden fields --}}
            <input type="hidden" name="start_time" value="{{ $start_time }}">
            <input type="hidden" name="end_time" value="{{ $end_time }}">

            {{-- Chọn bàn --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Chọn bàn <span class="text-red-500">*</span>
                </label>

                @if($tables->isEmpty())
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                        <p class="text-red-600 text-sm">😔 Không có bàn trống lúc này</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($tables as $table)
                            <label class="cursor-pointer">
                                <input type="radio" name="table_id"
                                       value="{{ $table->id }}"
                                       class="sr-only peer"
                                       {{ old('table_id') == $table->id ? 'checked' : '' }}>
                                <div class="border-2 border-gray-200 rounded-xl p-4
                                            peer-checked:border-orange-400
                                            peer-checked:bg-orange-50
                                            hover:border-orange-300 transition">
                                    <p class="font-semibold text-gray-800">{{ $table->name }}</p>
                                    <p class="text-xs text-gray-500">📍 {{ $table->area->name }}</p>
                                    <p class="text-xs text-gray-500">👥 {{ $table->capacity }} người</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif

                @error('table_id')
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Số người --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Số người <span class="text-red-500">*</span>
                </label>
                <input type="number" name="guest_count" min="1"
                       value="{{ old('guest_count') }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                              focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
                @error('guest_count')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tên khách --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tên khách
                    <span class="text-gray-400 font-normal">(tuỳ chọn)</span>
                </label>
                <input type="text" name="guest_name"
                       value="{{ old('guest_name') }}"
                       placeholder="Nhập tên khách nếu có..."
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                              focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
                @error('guest_name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Số điện thoại
                    <span class="text-gray-400 font-normal">(tuỳ chọn)</span>
                </label>
                <input type="text" name="guest_phone"
                       value="{{ old('guest_phone') }}"
                       placeholder="Nhập số điện thoại nếu có..."
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                              focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
                @error('guest_phone')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ghi chú --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                <textarea name="note" rows="3"
                          placeholder="VD: Sinh nhật, dị ứng hải sản,..."
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                                 focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">{{ old('note') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-3 rounded-xl text-white text-sm font-semibold
                               transition hover:-translate-y-0.5"
                        style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                    ✅ Xác nhận Walk-in
                </button>
                <a href="{{ route('manage.bookings.index') }}"
                   class="px-6 py-3 rounded-xl text-sm font-semibold bg-gray-100
                          text-gray-600 hover:bg-gray-200 transition">
                    Huỷ
                </a>
            </div>

        </form>
    </div>

</div>
@endsection