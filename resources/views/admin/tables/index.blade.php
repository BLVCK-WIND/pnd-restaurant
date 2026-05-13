@extends('layouts.admin')

@section('title', 'Bàn ăn')

@section('content')
<div>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Quản lý bàn ăn</h1>
        <a href="{{ route('admin.tables.create') }}"
           class="px-4 py-2 rounded-xl text-white text-sm font-semibold"
           style="background: linear-gradient(135deg, #c8622a, #f5a623);">
            + Thêm bàn
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.tables.index') }}"
          class="bg-white rounded-2xl shadow p-4 mb-6 flex flex-wrap gap-3">

        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Tìm tên bàn..."
               class="px-4 py-2 rounded-xl border border-gray-200 focus:outline-none
                      focus:border-orange-400 bg-gray-50 text-sm flex-1 min-w-40">

        <select name="area_id"
                class="px-4 py-2 rounded-xl border border-gray-200 focus:outline-none
                       focus:border-orange-400 bg-gray-50 text-sm">
            <option value="">Tất cả khu vực</option>
            @foreach($areas as $area)
                <option value="{{ $area->id }}"
                    {{ request('area_id') == $area->id ? 'selected' : '' }}>
                    {{ $area->name }}
                </option>
            @endforeach
        </select>

        <select name="status"
                class="px-4 py-2 rounded-xl border border-gray-200 focus:outline-none
                       focus:border-orange-400 bg-gray-50 text-sm">
            <option value="">Tất cả trạng thái</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                Hoạt động
            </option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                Ngưng hoạt động
            </option>
        </select>

        <button type="submit"
                class="px-4 py-2 rounded-xl text-white text-sm font-semibold"
                style="background: #c8622a;">
            Lọc
        </button>

        @if(request()->hasAny(['search', 'area_id', 'status']))
            <a href="{{ route('admin.tables.index') }}"
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
                    <th class="px-4 py-3 text-white font-medium">Tên bàn</th>
                    <th class="px-4 py-3 text-white font-medium">Khu vực</th>
                    <th class="px-4 py-3 text-white font-medium">Sức chứa</th>
                    <th class="px-4 py-3 text-white font-medium">Trạng thái</th>
                    <th class="px-4 py-3 text-white font-medium">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tables as $table)
                    @php
                        $statusConfig = [
                            'active'   => ['label' => 'Hoạt động',        'class' => 'bg-green-100 text-green-700'],
                            'inactive' => ['label' => 'Ngưng hoạt động',  'class' => 'bg-gray-100 text-gray-500'],
                        ];
                        $config = $statusConfig[$table->status] ?? $statusConfig['active'];
                    @endphp
                    <tr class="hover:bg-orange-50 transition">
                        <td class="px-4 py-3 text-gray-500">{{ $tables->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $table->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $table->area->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $table->capacity }} người</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $config['class'] }}">
                                {{ $config['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.tables.edit', $table) }}"
                                   class="px-3 py-1 rounded-lg text-xs font-medium bg-blue-50
                                          text-blue-600 hover:bg-blue-100 transition">
                                    Sửa
                                </a>
                                <form action="{{ route('admin.tables.destroy', $table) }}"
                                      method="POST"
                                      onsubmit="return confirm('Xoá bàn này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 rounded-lg text-xs font-medium
                                                   bg-red-50 text-red-600 hover:bg-red-100 transition">
                                        Xoá
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            Không có bàn nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tables->links() }}
    </div>

</div>
@endsection