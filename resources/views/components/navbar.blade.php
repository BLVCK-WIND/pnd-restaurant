<style>
    .nav-link {
        transition: all 0.2s ease;
        position: relative;
    }
    .nav-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: #f5a623;
        transition: width 0.3s ease;
    }
    .nav-link:hover::after,
    .nav-link.active::after {
        width: 100%;
    }
    .nav-link:hover,
    .nav-link.active {
        color: #f5a623;
    }
    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .flash-msg {
        animation: fadeUp 0.4s ease;
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

<header class="sticky top-0 z-50 shadow-lg"
        style="background: linear-gradient(135deg, #2c1a0e, #5c3317);">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('staff.dashboard') }}"
               class="flex items-center gap-2">
                <span class="text-2xl">🍜</span>
                <div>
                    <span class="text-white font-bold text-base">PND Restaurant</span>
                    <span class="text-orange-400 text-xs ml-2">
                        {{ auth()->user()->role === 'admin' ? 'Admin' : 'Staff' }}
                    </span>
                </div>
            </a>

            {{-- Navigation --}}
            <nav class="hidden md:flex items-center gap-1">

                @if(auth()->user()->role === 'admin')
                    {{-- ===== ADMIN NAV ===== --}}

                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-link px-3 py-2 text-orange-100 text-sm font-medium rounded-lg
                              {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        📊 Dashboard
                    </a>

                    {{-- Thực đơn --}}
                    <div class="dropdown-wrapper relative">
                        <button class="dropdown-toggle nav-link px-3 py-2 text-orange-100 text-sm font-medium rounded-lg flex items-center gap-1
                                       {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.menu-items.*') || request()->routeIs('admin.optiongroups.*') ? 'active' : '' }}">
                            🍽️ Thực đơn <span class="text-xs">▾</span>
                        </button>
                        <div class="dropdown hidden absolute top-full left-0 mt-2 w-48 bg-white rounded-xl shadow-xl overflow-hidden">
                            <a href="{{ route('admin.categories.index') }}"
                               class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition">
                                🗂️ Danh mục
                            </a>
                            <a href="{{ route('admin.menuitems.index') }}"
                               class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition">
                                🍜 Món ăn
                            </a>
                            <a href="{{ route('admin.optiongroups.index') }}"
                               class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition">
                                🍜 Group Option
                            </a>
                        </div>
                    </div>

                    {{-- Nhà hàng --}}
                    <div class="dropdown-wrapper relative">
                        <button class="dropdown-toggle nav-link px-3 py-2 text-orange-100 text-sm font-medium rounded-lg flex items-center gap-1
                                       {{ request()->routeIs('admin.areas.*') || request()->routeIs('admin.tables.*') || request()->routeIs('manage.bookings.*') || request()->routeIs('manage.orders.*') ? 'active' : '' }}">
                            🏠 Nhà hàng <span class="text-xs">▾</span>
                        </button>
                        <div class="dropdown hidden absolute top-full left-0 mt-2 w-48 bg-white rounded-xl shadow-xl overflow-hidden">
                            <a href="{{ route('admin.areas.index') }}"
                               class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition">
                                🪑 Khu vực
                            </a>
                            <a href="{{ route('admin.tables.index') }}"
                               class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition">
                                🪑 Bàn ăn
                            </a>
                            <a href="{{ route('manage.bookings.index') }}"
                               class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition">
                                📅 Đặt bàn
                            </a>
                            <a href="{{route('manage.orders.index')}}"
                               class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition">
                                🧾 Order
                            </a>
                        </div>
                    </div>

                    {{-- Nhân sự --}}
                    <div class="dropdown-wrapper relative">
                        <button class="dropdown-toggle nav-link px-3 py-2 text-orange-100 text-sm font-medium rounded-lg flex items-center gap-1
                        {{ request()->routeIs('admin.staffs.*') || request()->routeIs('admin.guests.*') || request()->routeIs('manage.schedules.*') ? 'active' : '' }}">
                            👥 Nhân sự <span class="text-xs">▾</span>
                        </button>
                        <div class="dropdown hidden absolute top-full left-0 mt-2 w-48 bg-white rounded-xl shadow-xl overflow-hidden">
                            <a href="{{route('admin.staffs.index')}}"
                               class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition">
                                👤 Nhân viên
                            </a>
                            <a href="{{route('admin.guests.index')}}"
                               class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition">
                                👤 Khách hàng
                            </a>
                            <a href="{{route('admin.schedules.index')}}"
                               class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition">
                                🕐 Phân ca
                            </a>
                        </div>
                    </div>

                    {{-- Báo cáo --}}
                    <div class="dropdown-wrapper relative">
                        <button class="dropdown-toggle nav-link px-3 py-2 text-orange-100 text-sm font-medium rounded-lg flex items-center gap-1
                        {{ request()->routeIs('admin.reviews.*') || request()->routeIs('admin.revenue.*')? 'active' : '' }}">
                            📈 Báo cáo <span class="text-xs">▾</span>
                        </button>
                        <div class="dropdown hidden absolute top-full left-0 mt-2 w-48 bg-white rounded-xl shadow-xl overflow-hidden">
                            <a href="{{route('admin.reviews.index')}}"
                               class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition">
                                ⭐ Review
                            </a>
                            <a href="{{route('admin.revenue.index')}}"
                               class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition">
                                📊 Doanh thu
                            </a>
                        </div>
                    </div>

                @else
                    {{-- ===== STAFF NAV ===== --}}
                    <a href="{{ route('staff.dashboard') }}"
                       class="nav-link px-3 py-2 text-orange-100 text-sm font-medium rounded-lg
                              {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                        📊 Dashboard
                    </a>

                    <a href="{{ route('manage.bookings.index') }}"
                       class="nav-link px-3 py-2 text-orange-100 text-sm font-medium rounded-lg
                              {{ request()->routeIs('manage.bookings.*') ? 'active' : '' }}">
                        📅 Đặt bàn
                    </a>

                    <a href="{{route('manage.orders.index')}}"
                       class="nav-link px-3 py-2 text-orange-100 text-sm font-medium rounded-lg
                                {{ request()->routeIs('manage.orders.*') ? 'active' : '' }}">
                        🧾 Order
                    </a>

                    <a href="{{route('staff.schedules.index')}}"
                       class="nav-link px-3 py-2 text-orange-100 text-sm font-medium rounded-lg
                                {{ request()->routeIs('staff.schedules.*') ? 'active' : '' }}">
                        🕐 Ca làm việc
                    </a>

                @endif

            </nav>

            {{-- User dropdown (dùng chung) --}}
            <div class="dropdown-wrapper relative">
                <button class="dropdown-toggle flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-white hover:bg-opacity-10 transition">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold"
                         style="background: linear-gradient(135deg, #c8622a, #f5a623);">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="text-orange-100 text-sm hidden md:block">
                        {{ auth()->user()->name }}
                    </span>
                    <span class="text-orange-300 text-xs">▾</span>
                </button>
                <div class="dropdown hidden absolute top-full right-0 mt-2 w-48 bg-white rounded-xl shadow-xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition">
                            🚪 Đăng xuất
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>

<script>
    function closeAll() {
        document.querySelectorAll('.dropdown').forEach(d => d.classList.add('hidden'));
    }
    document.querySelectorAll('.dropdown-toggle').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const dropdown = this.nextElementSibling;
            const isHidden = dropdown.classList.contains('hidden');
            closeAll();
            if (isHidden) dropdown.classList.remove('hidden');
        });
    });
    document.addEventListener('click', () => closeAll());
    document.querySelectorAll('.dropdown').forEach(d => {
        d.addEventListener('click', e => e.stopPropagation());
    });
</script>