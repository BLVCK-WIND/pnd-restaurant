<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — PND Restaurant</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    {{-- TOP NAVBAR --}}
    <x-navbar />

    {{-- FLASH MESSAGES --}}
    <div class="max-w-screen-xl mx-auto w-full px-6 pt-4">
        @if(session('success'))
            <div class="flash-msg px-4 py-3 rounded-xl text-sm text-green-700 bg-green-50 border border-green-200 mb-2">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flash-msg px-4 py-3 rounded-xl text-sm text-red-700 bg-red-50 border border-red-200 mb-2">
                ❌ {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 max-w-screen-xl mx-auto w-full px-6 py-6">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="text-center text-xs text-gray-400 py-4 border-t border-gray-200">
        © 2025 PND Restaurant. All rights reserved.
    </footer>

    @stack('scripts')
</body>
</html>