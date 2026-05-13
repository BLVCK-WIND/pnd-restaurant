<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function loginIndex()
    {
        if (Auth::check()) {
            return match(Auth::user()->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'staff' => redirect()->route('staff.dashboard'),
                default => redirect()->route('guest.index'),
            };
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Bước 1: Thử đăng nhập
        if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return back()
                ->withErrors(['email' => 'Email hoặc mật khẩu không đúng'])
                ->withInput($request->only('email'));
        }

        // Bước 2: Đăng nhập thành công - kiểm tra is_active
        // Phải check TRƯỚC khi regenerate và redirect
        if (!Auth::user()->is_active) {
            Auth::logout();
            return back()
                ->withErrors(['email' => 'Tài khoản của bạn đã bị khoá.'])
                ->withInput($request->only('email'));
        }

        // Bước 3: Hợp lệ hoàn toàn - regenerate session
        $request->session()->regenerate();

        // Bước 4: Redirect theo role
        return match(Auth::user()->role) {
            'admin' => redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công'),
            'staff' => redirect()->route('staff.dashboard')->with('success', 'Đăng nhập thành công'),
            default => redirect()->route('guest.index')->with('success', 'Đăng nhập thành công'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
