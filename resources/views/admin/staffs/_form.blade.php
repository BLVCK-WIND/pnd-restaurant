{{-- Tên --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Họ tên <span class="text-red-500">*</span>
    </label>
    <input type="text" name="name"
           value="{{ old('name', $staff->name ?? '') }}"
           placeholder="Nguyễn Văn A"
           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                  focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
    @error('name')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Email --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Email <span class="text-red-500">*</span>
    </label>
    <input type="email" name="email"
           value="{{ old('email', $staff->email ?? '') }}"
           placeholder="example@pnd.com"
           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                  focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
    @error('email')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Phone --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
    <input type="text" name="phone"
           value="{{ old('phone', $staff->phone ?? '') }}"
           placeholder="0901234567"
           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                  focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
    @error('phone')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Role --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Vai trò <span class="text-red-500">*</span>
    </label>
    <select name="role"
            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                   focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
        @foreach(['staff' => 'Nhân viên', 'admin' => 'Admin'] as $value => $label)
            <option value="{{ $value }}"
                {{ old('role', $staff->role ?? 'staff') == $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('role')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Password --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Mật khẩu
        @isset($staff)
            <span class="text-gray-400 font-normal">(để trống nếu không đổi)</span>
        @else
            <span class="text-red-500">*</span>
        @endisset
    </label>
    <input type="password" name="password"
           placeholder="Tối thiểu 8 ký tự"
           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                  focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
    @error('password')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Xác nhận password --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Xác nhận mật khẩu
    </label>
    <input type="password" name="password_confirmation"
           placeholder="Nhập lại mật khẩu"
           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none
                  focus:border-orange-400 focus:ring-2 focus:ring-orange-100 bg-gray-50">
</div>

{{-- Trạng thái --}}
<div class="flex items-center gap-3">
    <input type="checkbox" name="is_active" id="is_active" value="1"
           {{ old('is_active', $staff->is_active ?? true) ? 'checked' : '' }}
           class="w-4 h-4 accent-orange-500">
    <label for="is_active" class="text-sm font-medium text-gray-700">
        Tài khoản đang hoạt động
    </label>
</div>