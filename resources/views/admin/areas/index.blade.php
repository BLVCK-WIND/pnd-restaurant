@extends('layouts.admin')

@section('title', 'Khu vực')

@section('content')
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Khu vực nhà hàng</h1>
        <a href="{{ route('admin.areas.create') }}"
           class="px-4 py-2 rounded-xl text-white text-sm font-semibold"
           style="background: linear-gradient(135deg, #c8622a, #f5a623);">
            + Thêm khu vực
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead style="background: #2c1a0e;">
                <tr>
                    <th class="px-4 py-3 text-white font-medium">#</th>
                    <th class="px-4 py-3 text-white font-medium">Tên khu vực</th>
                    <th class="px-4 py-3 text-white font-medium">Mô tả</th>
                    <th class="px-4 py-3 text-white font-medium">Số bàn</th>
                    <th class="px-4 py-3 text-white font-medium">Trạng thái</th>
                    <th class="px-4 py-3 text-white font-medium">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($areas as $area)
                    <tr class="hover:bg-orange-50 transition">
                        <td class="px-4 py-3 text-gray-500">{{ $areas->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $area->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $area->description ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600">
                                {{ $area->tables()->count() }} bàn
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($area->is_active)
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    Hoạt động
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                    Ẩn
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.areas.edit', $area) }}"
                                   class="px-3 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                    Sửa
                                </a>
                                <form action="{{ route('admin.areas.destroy', $area) }}" method="POST"
                                      onsubmit="return confirm('Xoá khu vực này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 rounded-lg text-xs font-medium bg-red-50 text-red-600 hover:bg-red-100 transition">
                                        Xoá
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            Chưa có khu vực nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $areas->links() }}
    </div>

</div>
@endsection