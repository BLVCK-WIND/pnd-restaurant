@extends('layouts.admin')

@section('title', 'Option Groups')

@section('content')
<div>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Option Groups</h1>
        <a href="{{ route('admin.optiongroups.create') }}"
           class="px-4 py-2 rounded-xl text-white text-sm font-semibold"
           style="background: linear-gradient(135deg, #c8622a, #f5a623);">
            + Thêm Option Group
        </a>
    </div>

    <div class="space-y-4">
        @forelse($optionGroups as $group)
            <div class="bg-white rounded-2xl shadow p-5">
                <div class="flex items-start justify-between gap-4">

                    {{-- Thông tin group --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="font-semibold text-gray-800 text-lg">
                                {{ $group->name }}
                            </h3>
                            @if($group->is_required)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                             bg-red-100 text-red-600">
                                    Bắt buộc
                                </span>
                            @endif
                            @if($group->is_multiple)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                             bg-blue-100 text-blue-600">
                                    Chọn nhiều
                                </span>
                            @endif
                        </div>

                        {{-- Danh sách values --}}
                        <div class="flex flex-wrap gap-2">
                            @forelse($group->optionValues as $value)
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                             bg-gray-100 text-gray-600">
                                    {{ $value->name }}
                                    @if($value->extra_price > 0)
                                        <span class="text-orange-500">
                                            +{{ number_format($value->extra_price) }}đ
                                        </span>
                                    @endif
                                </span>
                            @empty
                                <span class="text-xs text-gray-400 italic">Chưa có values</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- Hành động --}}
                    <div class="flex gap-2 shrink-0">
                        <a href="{{ route('admin.optiongroups.edit', $group) }}"
                           class="px-3 py-1 rounded-lg text-xs font-medium bg-blue-50
                                  text-blue-600 hover:bg-blue-100 transition">
                            Sửa
                        </a>
                        <form action="{{ route('admin.optiongroups.destroy', $group) }}"
                              method="POST"
                              onsubmit="return confirm('Xoá option group này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-3 py-1 rounded-lg text-xs font-medium bg-red-50
                                           text-red-600 hover:bg-red-100 transition">
                                Xoá
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow p-10 text-center text-gray-400">
                <p class="text-4xl mb-3">⚙️</p>
                <p>Chưa có option group nào</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $optionGroups->links() }}
    </div>

</div>
@endsection