{{-- Tên khu vực --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Tên khu vực <span class="text-red-500">*</span>
    </label>
    <input type="text" name="name"
           value="{{ old('name', $area->name ?? '') }}"
           placeholder="VD: Tầng 1, Ngoài trời, VIP..."
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
              placeholder="Mô tả ngắn về khu vực này..."
              class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                     focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">{{ old('description', $area->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Trạng thái --}}
<div class="flex items-center gap-3">
    <input type="checkbox" name="is_active" id="is_active" value="1"
           {{ old('is_active', $area->is_active ?? true) ? 'checked' : '' }}
           class="w-4 h-4 accent-orange-500">
    <label for="is_active" class="text-sm font-medium text-gray-700">
        Khu vực đang hoạt động
    </label>
</div>