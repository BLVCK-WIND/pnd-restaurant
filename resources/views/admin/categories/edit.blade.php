@extends('layouts.admin')

@section('title', 'Sửa danh mục')

@section('content')
<div class="p-6 max-w-2xl">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.categories.index') }}"
           class="text-gray-400 hover:text-gray-600 transition">← Quay lại</a>
        <h1 class="text-2xl font-bold text-gray-800">Sửa danh mục</h1>
    </div>

    <div class="bg-white rounded-2xl shadow p-6">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST"
              enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Tên --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tên danh mục <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Mô tả --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ảnh hiện tại --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh danh mục</label>
                @if($category->image)
                    <div class="mb-2">
                        <img src="{{ Storage::url($category->image) }}"
                             alt="{{ $category->name }}"
                             class="w-24 h-24 rounded-xl object-cover border border-gray-200">
                        <p class="text-xs text-gray-400 mt-1">Ảnh hiện tại — upload ảnh mới để thay thế</p>
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50">
                @error('image')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Thứ tự --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự hiển thị</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
                @error('sort_order')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Trạng thái --}}
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 accent-orange-500">
                <label for="is_active" class="text-sm font-medium text-gray-700">
                    Hiển thị danh mục này
                </label>
            </div>

            {{-- Option Groups --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Option Groups
                    <span class="text-gray-400 font-normal">(chỉ áp dụng riêng cho món này)</span>
                </label>
                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-xl p-3 bg-gray-50">
                    @forelse($optionGroups as $group)
                        <label class="flex items-center gap-3 cursor-pointer hover:bg-white
                                    rounded-lg px-2 py-1.5 transition">
                            <input type="checkbox"
                                name="option_groups[]"
                                value="{{ $group->id }}"
                                {{ in_array($group->id, old('option_groups',
                                    isset($category) ? $category->optionGroups->pluck('id')->toArray() : []
                                )) ? 'checked' : '' }}
                                class="w-4 h-4 accent-orange-500">
                            <div>
                                <span class="text-sm font-medium text-gray-800">{{ $group->name }}</span>
                                @if($group->is_required)
                                    <span class="text-xs text-red-500 ml-1">*bắt buộc</span>
                                @endif
                                <span class="text-xs text-gray-400 ml-2">
                                    {{ $group->optionValues->pluck('name')->join(' / ') }}
                                </span>
                            </div>
                        </label>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-2">
                            Chưa có option group nào —
                            <a href="{{ route('admin.optiongroups.create') }}"
                            class="text-orange-500 hover:underline">Tạo mới</a>
                        </p>
                    @endforelse
                </div>
            </div>

            {{-- Button --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-3 rounded-xl text-white text-sm font-semibold transition hover:-translate-y-0.5"
                        style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                    Cập nhật
                </button>
                <a href="{{ route('admin.categories.index') }}"
                   class="px-6 py-3 rounded-xl text-sm font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    Huỷ
                </a>
            </div>

        </form>
    </div>

</div>
@endsection