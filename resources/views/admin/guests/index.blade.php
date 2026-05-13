@extends('layouts.admin')

@section('title', 'Quản lý khách hàng')

@section('content')
<div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Quản lý khách hàng</h1>
    </div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.guests.index') }}"
          class="flex flex-wrap gap-3 mb-5">

        {{-- Tìm kiếm --}}
        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="Tìm tên, email..."
               class="flex-1 min-w-48 px-4 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">

        {{-- Lọc trạng thái --}}
        <select name="is_active"
                class="px-4 py-2 rounded-xl border border-gray-200 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-300">
            <option value="">Tất cả trạng thái</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Hoạt động</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Vô hiệu</option>
        </select>

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

        @if(request()->hasAny(['search', 'is_active', 'date_from', 'date_to']))
            <a href="{{ route('admin.guests.index') }}"
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
                    <th class="px-4 py-3 text-white font-medium">Trạng thái</th>
                    <th class="px-4 py-3 text-white font-medium">Ngày tạo</th>
                    <th class="px-4 py-3 text-white font-medium">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($guests as $guest)
                    <tr class="hover:bg-orange-50 transition">
                        <td class="px-4 py-3 text-gray-500">{{ $guests->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0"
                                     style="background: linear-gradient(135deg, #4b7cf3, #6c9fff);">
                                    {{ strtoupper(substr($guest->name, 0, 1)) }}
                                </div>
                                {{ $guest->name }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $guest->email }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $guest->phone ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($guest->is_active)
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
                            {{ $guest->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                {{-- Toggle active --}}
                                <form action="{{ route('admin.guests.toggle', $guest) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="px-3 py-1 rounded-lg text-xs font-medium transition
                                                {{ $guest->is_active
                                                    ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100'
                                                    : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                                        {{ $guest->is_active ? 'Vô hiệu' : 'Kích hoạt' }}
                                    </button>
                                </form>

                                {{-- Xoá --}}
                                <form action="{{ route('admin.guests.destroy', $guest) }}" method="POST"
                                      onsubmit="return confirm('Xoá khách hàng {{ $guest->name }}?')">
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
                            @if(request()->hasAny(['search', 'is_active', 'date_from', 'date_to']))
                                Không tìm thấy khách hàng nào phù hợp
                            @else
                                Chưa có khách hàng nào
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $guests->links() }}
    </div>

</div>
@endsection