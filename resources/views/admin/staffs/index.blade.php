@extends('layouts.admin')

@section('title', 'Quản lý nhân viên')

@section('content')
<div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Quản lý nhân viên</h1>
        <a href="{{ route('admin.staffs.create') }}"
           class="px-4 py-2 rounded-xl text-white text-sm font-semibold"
           style="background: linear-gradient(135deg, #c8622a, #f5a623);">
            + Thêm nhân viên
        </a>
    </div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.staffs.index') }}"
          class="flex flex-wrap gap-3 mb-5">

        {{-- Tìm kiếm --}}
        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="Tìm tên, email..."
               class="flex-1 min-w-48 px-4 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">

        {{-- Lọc ngày tạo --}}
        <input type="date"
               name="date_from"
               value="{{ request('date_from') }}"
               class="px-4 py-2 rounded-xl border border-gray-200 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-300">
        <input type="date"
               name="date_to"
               value="{{ request('date_to') }}"
               class="px-4 py-2 rounded-xl border border-gray-200 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-300">

        <button type="submit"
                class="px-4 py-2 rounded-xl text-white text-sm font-medium"
                style="background: linear-gradient(135deg, #c8622a, #f5a623);">
            Tìm kiếm
        </button>

        @if(request()->hasAny(['search', 'role', 'date_from', 'date_to']))
            <a href="{{ route('admin.staffs.index') }}"
               class="px-4 py-2 rounded-xl text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Xoá bộ lọc
            </a>
        @endif

    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead style="background: #2c1a0e;">
                <tr>
                    <th class="px-4 py-3 text-white font-medium">#</th>
                    <th class="px-4 py-3 text-white font-medium">Tên</th>
                    <th class="px-4 py-3 text-white font-medium">Email</th>
                    <th class="px-4 py-3 text-white font-medium">SĐT</th>
                    <th class="px-4 py-3 text-white font-medium">Vai trò</th>
                    <th class="px-4 py-3 text-white font-medium">Trạng thái</th>
                    <th class="px-4 py-3 text-white font-medium">Ngày tạo</th>
                    <th class="px-4 py-3 text-white font-medium">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($staffs as $staff)
                    <tr class="hover:bg-orange-50 transition">
                        <td class="px-4 py-3 text-gray-500">{{ $staffs->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            <div class="flex items-center gap-2">
                                {{-- Avatar chữ cái --}}
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0"
                                     style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                                    {{ strtoupper(substr($staff->name, 0, 1)) }}
                                </div>
                                {{ $staff->name }}
                                {{-- Đánh dấu tài khoản đang đăng nhập --}}
                                @if($staff->id === auth()->id())
                                    <span class="text-xs text-orange-400">(bạn)</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $staff->email }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $staff->phone ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($staff->role === 'admin')
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                    Admin
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    Staff
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($staff->is_active)
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    Hoạt động
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                    Vô hiệu
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $staff->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                {{-- Sửa --}}
                                <a href="{{ route('admin.staffs.edit', $staff) }}"
                                class="px-3 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                    Sửa
                                </a>

                                @if($staff->id !== auth()->id())
                                    {{-- Toggle khoá/mở --}}
                                    <form action="{{ route('admin.staffs.toggle', $staff) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-3 py-1 rounded-lg text-xs font-medium transition
                                                    {{ $staff->is_active
                                                        ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100'
                                                        : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                                            {{ $staff->is_active ? 'Vô hiệu' : 'Kích hoạt' }}
                                        </button>
                                    </form>

                                    {{-- Xoá --}}
                                    <form action="{{ route('admin.staffs.destroy', $staff) }}" method="POST"
                                        onsubmit="return confirm('Xoá nhân viên {{ $staff->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1 rounded-lg text-xs font-medium bg-red-50 text-red-600 hover:bg-red-100 transition">
                                            Xoá
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            @if(request()->hasAny(['search', 'role', 'date_from', 'date_to']))
                                Không tìm thấy nhân viên nào phù hợp
                            @else
                                Chưa có nhân viên nào
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $staffs->links() }}
    </div>

</div>
@endsection