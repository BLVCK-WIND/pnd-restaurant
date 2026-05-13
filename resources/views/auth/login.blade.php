<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập — PND Restaurant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#c8622a',
                        secondary: '#f5a623',
                        dark: '#2c1a0e',
                    },
                    animation: {
                        'fade-up': 'fadeUp 0.6s ease-out',
                    },
                    keyframes: {
                        fadeUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .input-field {
            transition: all 0.3s ease;
        }
        .input-field:focus {
            transform: translateY(-2px);
        }
        .btn-login {
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(200, 98, 42, 0.4);
        }
        .btn-login:active {
            transform: translateY(0);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center"
      style="background: linear-gradient(135deg, #2c1a0e 0%, #5c3317 50%, #c8622a 100%);">

    {{-- Background decoration --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -left-20 w-96 h-96 rounded-full opacity-10"
             style="background: #f5a623;"></div>
        <div class="absolute -bottom-20 -right-20 w-96 h-96 rounded-full opacity-10"
             style="background: #f5a623;"></div>
    </div>

    {{-- Card --}}
    <div class="relative w-full max-w-md mx-4 animate-fade-up">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

            {{-- Header --}}
            <div class="px-8 pt-10 pb-6 text-center"
                 style="background: linear-gradient(135deg, #2c1a0e, #c8622a);">
                <div class="text-5xl mb-3">🍜</div>
                <h1 class="text-2xl font-bold text-white tracking-wide">PND Restaurant</h1>
                <p class="text-orange-200 text-sm mt-1">Chào mừng bạn trở lại</p>
            </div>

            {{-- Form --}}
            <div class="px-8 py-8">

                {{-- Flash error --}}
                @if($errors->any())
                    <div class="mb-4 px-4 py-3 rounded-xl text-sm text-red-700 bg-red-50 border border-red-200">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Flash success --}}
                @if(session('success'))
                    <div class="mb-4 px-4 py-3 rounded-xl text-sm text-green-700 bg-green-50 border border-green-200">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="example@email.com"
                            class="input-field w-full px-4 py-3 rounded-xl border border-gray-200
                                   focus:outline-none focus:border-primary focus:ring-2
                                   focus:ring-orange-100 text-gray-800 bg-gray-50"
                        >
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Mật khẩu
                        </label>
                        <input
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            class="input-field w-full px-4 py-3 rounded-xl border border-gray-200
                                   focus:outline-none focus:border-primary focus:ring-2
                                   focus:ring-orange-100 text-gray-800 bg-gray-50"
                        >
                        @error('password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Button --}}
                    <button
                        type="submit"
                        class="btn-login w-full py-3 rounded-xl text-white font-semibold
                               text-sm tracking-wide"
                        style="background: linear-gradient(135deg, #c8622a, #f5a623);"
                    >
                        Đăng nhập
                    </button>

                </form>

                {{-- Register link --}}
                <p class="text-center text-sm text-gray-500 mt-6">
                    Chưa có tài khoản?
                    <a href="{{ route('register.index') }}"
                       class="font-semibold hover:underline"
                       style="color: #c8622a;">
                        Đăng ký ngay
                    </a>
                </p>

            </div>
        </div>

        <p class="text-center text-white text-opacity-60 text-xs mt-4 opacity-60">
            © 2025 PND Restaurant. All rights reserved.
        </p>
    </div>

</body>
</html>