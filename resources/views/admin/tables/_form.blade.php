{{-- Khu vực --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Khu vực <span class="text-red-500">*</span>
    </label>
    <select name="area_id"
            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                   focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
        <option value="">-- Chọn khu vực --</option>
        @foreach($areas as $area)
            <option value="{{ $area->id }}"
                {{ old('area_id', $table->area_id ?? '') == $area->id ? 'selected' : '' }}>
                {{ $area->name }}
            </option>
        @endforeach
    </select>
    @error('area_id')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Tên bàn --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Tên bàn <span class="text-red-500">*</span>
    </label>
    <input type="text" name="name"
           value="{{ old('name', $table->name ?? '') }}"
           placeholder="VD: B01, VIP-01..."
           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                  focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
    @error('name')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Sức chứa --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Số người tối đa <span class="text-red-500">*</span>
    </label>
    <input type="number" name="capacity" min="1"
           value="{{ old('capacity', $table->capacity ?? '') }}"
           placeholder="VD: 2, 4, 6, 8..."
           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                  focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
    @error('capacity')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Trạng thái — chỉ hiện khi edit --}}
@isset($table)
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Trạng thái <span class="text-red-500">*</span>
    </label>
    <select name="status"
            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                   focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
        @foreach(['active' => 'Hoạt động', 'inactive' => 'Ngưng hoạt động'] as $value => $label)
            <option value="{{ $value }}"
                {{ old('status', $table->status ?? 'active') == $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('status')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>
@endisset