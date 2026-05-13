@extends('layouts.admin')

@section('title', 'Danh mục món ăn')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Danh mục món ăn</h1>
        <a href="{{ route('admin.categories.create') }}"
           class="px-4 py-2 rounded-xl text-white text-sm font-semibold"
           style="background: linear-gradient(135deg, #c8622a, #f5a623);">
            + Thêm danh mục
        </a>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.categories.index') }}"
          class="bg-white rounded-2xl shadow p-4 mb-6 flex flex-wrap gap-3">

        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Tìm danh mục..."
               class="px-4 py-2 rounded-xl border border-gray-200 focus:outline-none
                      focus:border-orange-400 bg-gray-50 text-sm flex-1 min-w-40">
        <button type="submit"
                class="px-4 py-2 rounded-xl text-white text-sm font-semibold"
                style="background: #c8622a;">
            Lọc
        </button>

        @if(request()->has('search'))
            <a href="{{ route('admin.categories.index') }}"
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
                    <th class="px-4 py-3 text-white font-medium w-8"></th>
                    <th class="px-1 py-3 text-white font-medium">Thứ tự</th>
                    <th class="px-5 py-3 text-white font-medium">Ảnh</th>
                    <th class="px-3 py-3 text-white font-medium">Tên danh mục</th>
                    <th class="px-3 py-3 text-white font-medium">Mô tả</th>
                    <th class="px-4 py-3 text-white font-medium">Trạng thái</th>
                    <th class="px-4 py-3 text-white font-medium">Hành động</th>
                </tr>
            </thead>
            <tbody id="sortable-table" class="divide-y divide-gray-100">
                @forelse($categories as $category)
                    <tr data-id="{{ $category->id }}"
                        data-sort-order="{{ $category->sort_order }}"
                        class="hover:bg-orange-50 transition">
                        <td class="px-4 py-3">
                            <span class="drag-handle text-gray-300 cursor-grab text-lg select-none">⠿</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 sort-order-cell">{{ $category->sort_order }}</td>
                        <td class="px-4 py-3">
                            @if($category->image)
                                <img src="{{ Storage::url($category->image) }}"
                                     alt="{{ $category->name }}"
                                     class="w-12 h-12 rounded-lg object-cover">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">
                                    No img
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $category->description ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($category->is_active)
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    Hiển thị
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                    Ẩn
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                   class="px-3 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                    Sửa
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                      onsubmit="return confirm('Bạn có chắc muốn xoá danh mục này không?')">
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
                            Chưa có danh mục nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $categories->links() }}
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    const tbody = document.getElementById('sortable-table');

    Sortable.create(tbody, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'bg-orange-100',

        onEnd: function () {
            const rows = [...tbody.querySelectorAll('tr[data-id]')];

            // Lấy danh sách sort_order ban đầu (trước khi kéo) theo thứ tự DOM cũ
            // → dùng data-sort-order đã gắn vào từng row khi render
            const originalOrders = rows.map(row => parseInt(row.dataset.sortOrder));

            // Sắp xếp lại: sort_order nhỏ nhất → lớn nhất để giữ nguyên thứ tự tương đối
            const sortedOrders = [...originalOrders].sort((a, b) => a - b);

            // Gán lại sort_order cho từng row theo thứ tự DOM mới
            rows.forEach((row, index) => {
                row.dataset.sortOrder = sortedOrders[index];
                row.querySelector('.sort-order-cell').textContent = sortedOrders[index];
            });

            // Gửi lên server: mỗi item gồm id + sort_order mới
            const items = rows.map(row => ({
                id: parseInt(row.dataset.id),
                sort_order: parseInt(row.dataset.sortOrder),
            }));

            fetch('{{ route('admin.categories.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ items }),
            })
            .then(res => res.json())
            .then(data => console.log(data.message))
            .catch(err => console.error('Lỗi cập nhật thứ tự:', err));
        }
    });
</script>
@endpush