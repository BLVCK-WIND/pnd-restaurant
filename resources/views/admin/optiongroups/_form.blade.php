{{-- Tên group --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Tên group <span class="text-red-500">*</span>
    </label>
    <input type="text" name="name"
           value="{{ old('name', $optiongroup->name ?? '') }}"
           placeholder="VD: Độ đá, Độ ngọt, Topping..."
           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                  focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
    @error('name')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Checkbox --}}
<div class="flex gap-6">
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="is_required" value="1"
               {{ old('is_required', $optiongroup->is_required ?? false) ? 'checked' : '' }}
               class="w-4 h-4 accent-orange-500">
        <span class="text-sm font-medium text-gray-700">Bắt buộc chọn</span>
    </label>

    <label class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="is_multiple" value="1"
               {{ old('is_multiple', $optiongroup->is_multiple ?? false) ? 'checked' : '' }}
               class="w-4 h-4 accent-orange-500">
        <span class="text-sm font-medium text-gray-700">Cho chọn nhiều</span>
    </label>
</div>

{{-- Values --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-3">
        Các lựa chọn <span class="text-red-500">*</span>
    </label>

    <div id="values-container" class="space-y-2 mb-3">
        {{-- Nếu đang edit — hiện values cũ --}}
        @isset($optiongroup)
            @foreach($optiongroup->optionValues as $index => $value)
                <div class="value-row flex gap-2 items-center">
                    <input type="text"
                           name="values[{{ $index }}][name]"
                           value="{{ old("values.$index.name", $value->name) }}"
                           placeholder="Tên lựa chọn"
                           class="flex-1 px-4 py-2 rounded-xl border border-gray-200
                                  focus:outline-none focus:border-orange-400 bg-gray-50 text-sm">
                    <input type="number"
                           name="values[{{ $index }}][extra_price]"
                           value="{{ old("values.$index.extra_price", $value->extra_price) }}"
                           placeholder="Phụ thu (đ)"
                           min="0"
                           class="w-36 px-4 py-2 rounded-xl border border-gray-200
                                  focus:outline-none focus:border-orange-400 bg-gray-50 text-sm">
                    <button type="button" onclick="removeValue(this)"
                            class="w-8 h-8 rounded-full bg-red-50 text-red-500
                                   hover:bg-red-100 transition text-sm font-bold">
                        ✕
                    </button>
                </div>
            @endforeach
        @else
            {{-- Trang create — hiện 1 dòng trống mặc định --}}
            <div class="value-row flex gap-2 items-center">
                <input type="text"
                       name="values[0][name]"
                       value="{{ old('values.0.name') }}"
                       placeholder="Tên lựa chọn"
                       class="flex-1 px-4 py-2 rounded-xl border border-gray-200
                              focus:outline-none focus:border-orange-400 bg-gray-50 text-sm">
                <input type="number"
                       name="values[0][extra_price]"
                       value="{{ old('values.0.extra_price', 0) }}"
                       placeholder="Phụ thu (đ)"
                       min="0"
                       class="w-36 px-4 py-2 rounded-xl border border-gray-200
                              focus:outline-none focus:border-orange-400 bg-gray-50 text-sm">
                <button type="button" onclick="removeValue(this)"
                        class="w-8 h-8 rounded-full bg-red-50 text-red-500
                               hover:bg-red-100 transition text-sm font-bold">
                    ✕
                </button>
            </div>
        @endisset
    </div>

    {{-- Nút thêm value --}}
    <button type="button" onclick="addValue()"
            class="px-4 py-2 rounded-xl text-sm font-medium border border-dashed
                   border-orange-300 text-orange-500 hover:bg-orange-50 transition">
        + Thêm lựa chọn
    </button>

    @error('values')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
    @error('values.*.name')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

@push('scripts')
<script>
    let valueIndex = {{ isset($optiongroup) ? $optiongroup->optionValues->count() : 1 }};

    function addValue() {
        const container = document.getElementById('values-container');
        const row = document.createElement('div');
        row.className = 'value-row flex gap-2 items-center';
        row.innerHTML = `
            <input type="text"
                   name="values[${valueIndex}][name]"
                   placeholder="Tên lựa chọn"
                   class="flex-1 px-4 py-2 rounded-xl border border-gray-200
                          focus:outline-none focus:border-orange-400 bg-gray-50 text-sm">
            <input type="number"
                   name="values[${valueIndex}][extra_price]"
                   placeholder="Phụ thu (đ)"
                   value="0" min="0"
                   class="w-36 px-4 py-2 rounded-xl border border-gray-200
                          focus:outline-none focus:border-orange-400 bg-gray-50 text-sm">
            <button type="button" onclick="removeValue(this)"
                    class="w-8 h-8 rounded-full bg-red-50 text-red-500
                           hover:bg-red-100 transition text-sm font-bold">
                ✕
            </button>
        `;
        container.appendChild(row);
        valueIndex++;
    }

    function removeValue(btn) {
        const rows = document.querySelectorAll('.value-row');
        if (rows.length <= 1) {
            alert('Phải có ít nhất 1 lựa chọn');
            return;
        }
        btn.closest('.value-row').remove();
    }
</script>
@endpush