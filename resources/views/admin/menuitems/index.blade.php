@extends('layouts.admin')

@section('title', 'Món ăn')

@section('content')
<div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Danh sách món ăn</h1>
        <a href="{{ route('admin.menuitems.create') }}"
           class="px-4 py-2 rounded-xl text-white text-sm font-semibold"
           style="background: linear-gradient(135deg, #c8622a, #f5a623);">
            + Thêm món ăn
        </a>
    </div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.menuitems.index') }}"
          class="bg-white rounded-2xl shadow p-4 mb-6 flex flex-wrap gap-3">

        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Tìm tên món ăn..."
               class="px-4 py-2 rounded-xl border border-gray-200 focus:outline-none
                      focus:border-orange-400 bg-gray-50 text-sm flex-1 min-w-40">

        <select name="category_id"
                class="px-4 py-2 rounded-xl border border-gray-200 focus:outline-none
                       focus:border-orange-400 bg-gray-50 text-sm">
            <option value="">Tất cả danh mục</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    {{ request('area_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <select name="sort"
                class="px-4 py-2 rounded-xl border border-gray-200 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-300">
            <option value="">Mặc định</option>
            <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected' : '' }}>Giá tăng dần</option>
            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
        </select>

        <button type="submit"
                class="px-4 py-2 rounded-xl text-white text-sm font-semibold"
                style="background: #c8622a;">
            Lọc
        </button>

        @if(request()->hasAny(['search', 'area_id', 'status']))
            <a href="{{ route('admin.menuitems.index') }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold bg-gray-100
                      text-gray-600 hover:bg-gray-200 transition">
                Xoá lọc
            </a>
        @endif

    </form>


    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead style="background: #2c1a0e;">
                <tr>
                    <th class="px-4 py-3 text-white font-medium">#</th>
                    <th class="px-4 py-3 text-white font-medium">Ảnh</th>
                    <th class="px-4 py-3 text-white font-medium">Tên món</th>
                    <th class="px-4 py-3 text-white font-medium">Danh mục</th>
                    <th class="px-4 py-3 text-white font-medium">Giá</th>
                    <th class="px-4 py-3 text-white font-medium">Trạng thái</th>
                    <th class="px-4 py-3 text-white font-medium">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($menuItems as $item)
                    <tr class="hover:bg-orange-50 transition">
                        <td class="px-4 py-3 text-gray-500">{{ $menuItems->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3">
                            @if($item->image)
                                <img src="{{ Storage::url($item->image) }}"
                                     alt="{{ $item->name }}"
                                     class="w-12 h-12 rounded-lg object-cover">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">
                                    No img
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $item->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $item->category->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-800 font-medium">
                            {{ number_format($item->price) }}đ
                        </td>
                        <td class="px-4 py-3">
                            @if($item->status === 'active')
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    Đang bán
                                </span>
                            @elseif($item->status === 'out_of_stock')
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                    Hết món
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                    Ẩn
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.menuitems.edit', $item) }}"
                                   class="px-3 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                    Sửa
                                </a>
                                <form action="{{ route('admin.menuitems.destroy', $item) }}" method="POST"
                                      onsubmit="return confirm('Bạn có chắc muốn xoá món này không?')">
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
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                            @if(request()->hasAny(['search', 'category_id', 'sort']))
                                Không tìm thấy món ăn nào phù hợp
                            @else
                                Chưa có món ăn nào
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $menuItems->links() }}
    </div>

</div>
@endsection