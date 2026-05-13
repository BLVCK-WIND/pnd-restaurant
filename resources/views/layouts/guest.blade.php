<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PND Restaurant')</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,600;0,700;1,400&family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary-container": "#ea6b1e",
                        "surface-container-high": "#292a29",
                        "on-background": "#e3e2e0",
                        "surface-bright": "#383938",
                        "surface-container-highest": "#343533",
                        "primary-fixed": "#ffdbcc",
                        primary: "#ffb693",
                        "inverse-surface": "#e3e2e0",
                        background: "#121412",
                        "secondary-fixed-dim": "#e9c349",
                        "on-primary": "#562000",
                        "inverse-primary": "#a04100",
                        "error-container": "#93000a",
                        "primary-fixed-dim": "#ffb693",
                        "surface-variant": "#343533",
                        "surface-container-low": "#1a1c1a",
                        "on-secondary-container": "#342800",
                        "surface-dim": "#121412",
                        "on-surface": "#e3e2e0",
                        "secondary-container": "#af8d11",
                        "secondary-fixed": "#ffe088",
                        "surface-container-lowest": "#0d0f0d",
                        "on-primary-container": "#4b1b00",
                        "surface-tint": "#ffb693",
                        "inverse-on-surface": "#2f312f",
                        outline: "#a78b7e",
                        "on-secondary": "#3c2f00",
                        "tertiary-container": "#929090",
                        surface: "#121412",
                        "on-surface-variant": "#e0c0b2",
                        "outline-variant": "#584238",
                        secondary: "#e9c349",
                        tertiary: "#c8c6c5",
                        "surface-container": "#1f201e",
                    },
                    spacing: {
                        "container-max": "1200px",
                        gutter: "24px",
                        "item-gap": "48px",
                        "section-padding": "80px",
                    },
                    fontFamily: {
                        serif: ['"Noto Serif"', 'serif'],
                        sans: ['"Work Sans"', 'sans-serif'],
                    },
                }
            }
        };
    </script>

    <style>
        :root {
            --primary: #ffb693;
            --primary-container: #ea6b1e;
            --secondary: #e9c349;
            --bg: #121412;
            --surface: #1f201e;
            --outline: #584238;
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--bg);
            color: #e3e2e0;
            font-family: 'Work Sans', sans-serif;
            overflow-x: hidden;
        }

        .material-symbols-outlined {
            font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24;
        }

        /* ── Navbar ── */
        #navbar {
            transition: background 0.4s ease, border-color 0.4s ease, backdrop-filter 0.4s ease;
        }
        #navbar.scrolled {
            background: rgba(18, 20, 18, 0.92) !important;
            backdrop-filter: blur(16px);
            border-color: rgba(88, 66, 56, 0.6);
        }

        .nav-link {
            position: relative;
            font-family: 'Work Sans', sans-serif;
            font-weight: 500;
            font-size: 0.875rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #e0c0b2;
            text-decoration: none;
            padding-bottom: 4px;
            transition: color 0.2s;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            width: 0; height: 1px;
            background: var(--primary);
            transition: width 0.3s ease;
        }
        .nav-link:hover { color: var(--primary); }
        .nav-link:hover::after { width: 100%; }
        .nav-link.active { color: var(--primary); }
        .nav-link.active::after { width: 100%; }

        /* ── Hamburger ── */
        .hamburger { cursor: pointer; }
        .hamburger span {
            display: block; width: 24px; height: 1.5px;
            background: #e3e2e0; margin: 5px 0;
            transition: all 0.3s ease;
        }
        .hamburger.open span:nth-child(1) { transform: rotate(45deg) translate(4.5px, 4.5px); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: rotate(-45deg) translate(4.5px, -4.5px); }

        /* ── Mobile menu ── */
        #mobile-menu {
            transform: translateY(-10px);
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
        }
        #mobile-menu.open {
            transform: translateY(0);
            opacity: 1;
            pointer-events: all;
        }

        /* ── Flash message ── */
        .flash-success {
            background: linear-gradient(135deg, rgba(234,107,30,0.15), rgba(234,107,30,0.05));
            border-left: 3px solid var(--primary-container);
            animation: slideDown 0.4s ease;
        }
        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to   { transform: translateY(0);     opacity: 1; }
        }

        /* ── Footer ── */
        .footer-link {
            color: #e0c0b2;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }
        .footer-link:hover { color: var(--secondary); }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0d0f0d; }
        ::-webkit-scrollbar-thumb { background: #584238; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary-container); }

        /* ── Page transition ── */
        main { animation: pageFadeIn 0.5s ease; }
        @keyframes pageFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Btn CTA ── */
        .btn-cta {
            background: var(--primary-container);
            color: #4b1b00;
            font-family: 'Work Sans', sans-serif;
            font-weight: 700;
            font-size: 0.75rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            padding: 10px 24px;
            border: none;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-cta:hover { box-shadow: 0 0 20px rgba(234,107,30,0.4); transform: translateY(-1px); }
        .btn-cta:active { transform: scale(0.97); }

        @yield('page-styles')
    </style>

    @yield('head-extra')
</head>
<body>

    {{-- ══════════════════ NAVBAR ══════════════════ --}}
    <header id="navbar" class="fixed top-0 left-0 right-0 z-50 border-b border-outline-variant"
            style="background: rgba(18,20,18,0.85); backdrop-filter: blur(12px);">
        <div class="flex justify-between items-center h-20 px-6 max-w-[1200px] mx-auto">

            {{-- Logo --}}
            <a href="{{ route('guest.index') }}"
               class="font-serif text-2xl font-bold tracking-tighter text-primary hover:opacity-80 transition-opacity">
                PND Restaurant
            </a>

            {{-- Desktop Nav --}}
            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('guest.index') }}"
                   class="nav-link {{ request()->routeIs('guest.index') ? 'active' : '' }}">
                    Trang chủ
                </a>
                <a href="{{ route('guest.menu') }}"
                   class="nav-link {{ request()->routeIs('guest.menu') ? 'active' : '' }}">
                    Thực đơn
                </a>
                <a href="{{ route('guest.about') }}"
                   class="nav-link {{ request()->routeIs('guest.about') ? 'active' : '' }}">
                    Về chúng tôi
                </a>
                <a href="{{ route('guest.contact') }}"
                   class="nav-link {{ request()->routeIs('guest.contact') ? 'active' : '' }}">
                    Liên hệ
                </a>

                @auth
                    <form action="{{ route('logout') }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="nav-link" style="background:none;border:none;cursor:pointer;">
                            Đăng xuất
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Đăng nhập</a>
                @endauth
            </nav>

            {{-- CTA + Hamburger --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('guest.bookings.index') }}" class="btn-cta hidden md:inline-block">
                    Đặt bàn
                </a>
                <button class="hamburger md:hidden" id="hamburger-btn" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu"
             class="md:hidden border-t border-outline-variant bg-surface-container-low px-6 py-6 space-y-4">
            <a href="{{ route('guest.index') }}"
               class="nav-link block {{ request()->routeIs('guest.index') ? 'active' : '' }}">
                Trang chủ
            </a>
            <a href="{{ route('guest.menu') }}"
               class="nav-link block {{ request()->routeIs('guest.menu') ? 'active' : '' }}">
                Thực đơn
            </a>
            <a href="{{ route('guest.about') }}"
               class="nav-link block {{ request()->routeIs('guest.about') ? 'active' : '' }}">
                Về chúng tôi
            </a>
            <a href="{{ route('guest.contact') }}"
               class="nav-link block {{ request()->routeIs('guest.contact') ? 'active' : '' }}">
                Liên hệ
            </a>
            <a href="{{ route('guest.contact') }}" class="btn-cta block text-center mt-4">
                Đặt bàn
            </a>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="fixed top-24 left-1/2 -translate-x-1/2 z-40 flash-success px-8 py-4 text-primary font-sans text-sm tracking-wide">
            <span class="material-symbols-outlined align-middle mr-2 text-base">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Main content --}}
    <main>
        @yield('content')
    </main>

    {{-- ══════════════════ FOOTER ══════════════════ --}}
    <footer class="bg-surface-container-lowest border-t border-outline-variant pt-16 pb-8 mt-0">
        <div class="max-w-[1200px] mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">

                {{-- Brand --}}
                <div class="md:col-span-1">
                    <div class="font-serif text-2xl font-bold text-primary mb-4">PND Restaurant</div>
                    <p class="text-on-surface-variant text-sm leading-relaxed mb-6">
                        Tinh hoa ẩm thực thượng lưu trong không gian bí ẩn và đẳng cấp bậc nhất Sài Gòn.
                    </p>
                    <div class="flex gap-3">
                        <a href="#" class="w-9 h-9 border border-outline-variant flex items-center justify-center text-on-surface-variant hover:border-primary hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-base">photo_camera</span>
                        </a>
                        <a href="#" class="w-9 h-9 border border-outline-variant flex items-center justify-center text-on-surface-variant hover:border-primary hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-base">social_leaderboard</span>
                        </a>
                    </div>
                </div>

                {{-- Thực đơn --}}
                <div>
                    <h4 class="text-xs font-sans font-bold uppercase tracking-widest text-primary mb-5">Thực đơn</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('guest.menu') }}" class="footer-link">Món chính</a></li>
                        <li><a href="{{ route('guest.menu') }}" class="footer-link">Khai vị</a></li>
                        <li><a href="{{ route('guest.menu') }}" class="footer-link">Tráng miệng</a></li>
                        <li><a href="{{ route('guest.menu') }}" class="footer-link">Rượu vang</a></li>
                    </ul>
                </div>

                {{-- Khám phá --}}
                <div>
                    <h4 class="text-xs font-sans font-bold uppercase tracking-widest text-primary mb-5">Khám phá</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('guest.index') }}" class="footer-link">Trang chủ</a></li>
                        <li><a href="{{ route('guest.about') }}" class="footer-link">Về chúng tôi</a></li>
                        <li><a href="{{ route('guest.contact') }}" class="footer-link">Liên hệ</a></li>
                    </ul>
                </div>

                {{-- Liên hệ --}}
                <div>
                    <h4 class="text-xs font-sans font-bold uppercase tracking-widest text-primary mb-5">Liên hệ</h4>
                    <ul class="space-y-3 text-sm text-on-surface-variant">
                        <li class="flex gap-2 items-start">
                            <span class="material-symbols-outlined text-base text-primary-container mt-0.5">location_on</span>
                            123 Đường Lê Lợi, Q.1, TP.HCM
                        </li>
                        <li class="flex gap-2 items-center">
                            <span class="material-symbols-outlined text-base text-primary-container">call</span>
                            +84 28 3456 7890
                        </li>
                        <li class="flex gap-2 items-center">
                            <span class="material-symbols-outlined text-base text-primary-container">schedule</span>
                            18:00 — 23:00, Hàng ngày
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Divider + copyright --}}
            <div class="border-t border-outline-variant pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-on-surface-variant text-xs">© 2024 PND Restaurant. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="footer-link">Privacy Policy</a>
                    <a href="#" class="footer-link">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // ── Navbar scroll effect ──
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 60);
        });

        // ── Hamburger toggle ──
        const btn = document.getElementById('hamburger-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        btn.addEventListener('click', () => {
            btn.classList.toggle('open');
            mobileMenu.classList.toggle('open');
        });

        // ── Auto-hide flash ──
        const flash = document.querySelector('.flash-success');
        if (flash) setTimeout(() => flash.style.opacity = '0', 3500);
    </script>

    @yield('scripts')
</body>
</html>