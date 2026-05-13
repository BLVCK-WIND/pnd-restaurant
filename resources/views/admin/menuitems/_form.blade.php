{{-- Danh mục --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Danh mục <span class="text-red-500">*</span>
    </label>
    <select name="category_id"
            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                   focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
        <option value="">-- Chọn danh mục --</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}"
                {{ old('category_id', $menuitem->category_id ?? '') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Tên món --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Tên món ăn <span class="text-red-500">*</span>
    </label>
    <input type="text" name="name"
           value="{{ old('name', $menuitem->name ?? '') }}"
           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                  focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
    @error('name')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Mô tả --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
    <textarea name="description" rows="3"
              class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                     focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">{{ old('description', $menuitem->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Giá --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Giá (VNĐ) <span class="text-red-500">*</span>
    </label>
    <input type="number" name="price" min="20000" step="1000"
           value="{{ old('price', $menuitem->price ?? '') }}"
           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                  focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
    @error('price')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Trạng thái --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Trạng thái <span class="text-red-500">*</span>
    </label>
    <select name="status"
            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                   focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
        @foreach(['active' => 'Đang bán', 'out_of_stock' => 'Hết món', 'inactive' => 'Ngừng bán'] as $value => $label)
            <option value="{{ $value }}"
                {{ old('status', $menuitem->status ?? 'active') == $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('status')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Ảnh --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh món ăn</label>
    @if(isset($menuitem) && $menuitem->image)
        <div class="mb-2">
            <img src="{{ Storage::url($menuitem->image) }}"
                 alt="{{ $menuitem->name }}"
                 class="w-24 h-24 rounded-xl object-cover border border-gray-200">
            <p class="text-xs text-gray-400 mt-1">Upload ảnh mới để thay thế</p>
        </div>
    @endif
    <input type="file" name="image" accept="image/*"
           class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50">
    @error('image')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Option Groups --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Option Groups
        <span class="text-gray-400 font-normal">(chỉ áp dụng riêng cho món này)</span>
    </label>

    @php
        // Gộp ID từ chính món ăn + từ danh mục của nó
        $checkedIds = old('option_groups',
            isset($menuitem)
                ? $menuitem->optionGroups->pluck('id')
                    ->merge($menuitem->category?->optionGroups->pluck('id') ?? [])
                    ->unique()
                    ->toArray()
                : []
        );
    @endphp

    <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-xl p-3 bg-gray-50">
        @forelse($optionGroups as $group)
            <label class="flex items-center gap-3 cursor-pointer hover:bg-white
                          rounded-lg px-2 py-1.5 transition">
                <input type="checkbox"
                       name="option_groups[]"
                       value="{{ $group->id }}"
                       {{ in_array($group->id, $checkedIds) ? 'checked' : '' }}
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