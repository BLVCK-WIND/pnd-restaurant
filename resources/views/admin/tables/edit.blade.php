@extends('layouts.admin')

@section('title', 'Sửa bàn ăn')

@section('content')
<div class="max-w-2xl">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.tables.index') }}"
           class="text-gray-400 hover:text-gray-600 transition">← Quay lại</a>
        <h1 class="text-2xl font-bold text-gray-800">Sửa bàn — {{ $table->name }}</h1>
    </div>

    <div class="bg-white rounded-2xl shadow p-6">
        <form action="{{ route('admin.tables.update', $table) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            @include('admin.tables._form')

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-3 rounded-xl text-white text-sm font-semibold
                               transition hover:-translate-y-0.5"
                        style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                    Cập nhật
                </button>
                <a href="{{ route('admin.tables.index') }}"
                   class="px-6 py-3 rounded-xl text-sm font-semibold bg-gray-100
                          text-gray-600 hover:bg-gray-200 transition">
                    Huỷ
                </a>
            </div>

        </form>
    </div>

</div>
@endsection