@extends('layouts.admin')

@section('title', 'Quản lý Review')

@section('content')
<div>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Quản lý Review</h1>
    </div>

    {{-- Tabs trạng thái --}}
    <div class="flex gap-2 mb-6">
        @foreach([
            'pending'  => ['label' => 'Chờ duyệt',  'class' => 'bg-yellow-100 text-yellow-700'],
            'approved' => ['label' => 'Đã duyệt',   'class' => 'bg-green-100 text-green-700'],
            'rejected' => ['label' => 'Từ chối',    'class' => 'bg-red-100 text-red-700'],
        ] as $value => $config)
            <a href="{{ route('admin.reviews.index', array_merge(request()->except('status', 'page'), ['status' => $value])) }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold transition
                      {{ $status === $value ? $config['class'] : 'bg-white text-gray-500 hover:bg-gray-50 shadow-sm' }}">
                {{ $config['label'] }}
            </a>
        @endforeach
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.reviews.index') }}"
          class="bg-white rounded-2xl shadow p-4 mb-6 flex flex-wrap gap-3">

        <input type="hidden" name="status" value="{{ $status }}">

        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Tìm tên khách..."
               class="px-4 py-2 rounded-xl border border-gray-200 focus:outline-none
                      focus:border-orange-400 bg-gray-50 text-sm flex-1 min-w-40">

        <select name="rating"
                class="px-4 py-2 rounded-xl border border-gray-200 focus:outline-none
                       focus:border-orange-400 bg-gray-50 text-sm">
            <option value="">Tất cả số sao</option>
            @for($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                    {{ str_repeat('★', $i) . str_repeat('☆', 5-$i) }} {{ $i }} sao
                </option>
            @endfor
        </select>

        <button type="submit"
                class="px-4 py-2 rounded-xl text-white text-sm font-semibold"
                style="background: #c8622a;">
            Lọc
        </button>

        @if(request()->hasAny(['search', 'table', 'rating']))
            <a href="{{ route('admin.reviews.index', ['status' => $status]) }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold bg-gray-100
                      text-gray-600 hover:bg-gray-200 transition">
                Xoá lọc
            </a>
        @endif

    </form>

    {{-- Danh sách review --}}
    <div class="space-y-4">
        @forelse($reviews as $review)
            <div class="bg-white rounded-2xl shadow p-5">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">

                    {{-- Thông tin --}}
                    <div class="flex-1 space-y-2">

                        {{-- Khách + bàn --}}
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center
                                            text-white text-sm font-bold"
                                     style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-800">{{ $review->user->name }}</span>
                            </div>
                            <span class="text-gray-300">—</span>
                            <span class="text-sm text-gray-500">
                                🪑 {{ $review->booking->table->name }}
                                ({{ $review->booking->table->area->name }})
                            </span>
                            <span class="text-sm text-gray-400">
                                📅 {{ $review->booking->start_time->format('d/m/Y') }}
                            </span>
                        </div>

                        {{-- Số sao --}}
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <span style="color: {{ $i <= $review->rating ? '#e9c349' : '#d1d5db' }}; font-size:1.1rem;">
                                    ★
                                </span>
                            @endfor
                            <span class="text-sm text-gray-500 ml-1">{{ $review->rating }}/5</span>
                        </div>

                        {{-- Comment --}}
                        @if($review->comment)
                            <p class="text-sm text-gray-600 leading-relaxed bg-gray-50
                                      rounded-xl px-4 py-3 border-l-2 border-orange-300">
                                "{{ $review->comment }}"
                            </p>
                        @else
                            <p class="text-sm text-gray-400 italic">Không có nhận xét</p>
                        @endif

                        {{-- Thời gian gửi --}}
                        <p class="text-xs text-gray-400">
                            Gửi lúc {{ $review->created_at->format('H:i d/m/Y') }}
                        </p>

                    </div>

                    {{-- Hành động --}}
                    <div class="flex flex-row md:flex-col gap-2 shrink-0">

                        @if($status === 'pending')
                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full px-4 py-2 rounded-xl text-sm font-medium
                                               bg-green-50 text-green-600 hover:bg-green-100 transition">
                                    ✅ Duyệt
                                </button>
                            </form>
                            <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full px-4 py-2 rounded-xl text-sm font-medium
                                               bg-red-50 text-red-600 hover:bg-red-100 transition">
                                    ❌ Từ chối
                                </button>
                            </form>
                        @endif

                        @if($status === 'approved')
                            <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full px-4 py-2 rounded-xl text-sm font-medium
                                               bg-red-50 text-red-600 hover:bg-red-100 transition">
                                    ❌ Từ chối
                                </button>
                            </form>
                        @endif

                        @if($status === 'rejected')
                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full px-4 py-2 rounded-xl text-sm font-medium
                                               bg-green-50 text-green-600 hover:bg-green-100 transition">
                                    ✅ Duyệt lại
                                </button>
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow p-10 text-center text-gray-400">
                <p class="text-4xl mb-3">⭐</p>
                <p>Không có review nào</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $reviews->links() }}
    </div>

</div>
@endsection