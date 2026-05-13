<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function registerIndex()
    {
        // Đã đăng nhập rồi thì không cho vào trang register nữa
        if (Auth::check()) {
            return match(Auth::user()->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'staff' => redirect()->route('staff.dashboard'),
                default => redirect()->route('guest.index'),
            };
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email', // email không được trùng
            'password' => 'required|confirmed|min:6',
            'phone'    => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'phone'    => $data['phone'] ?? null,
            'role'     => 'guest',
            'is_active'=> true,
        ]);

        // Đăng nhập luôn sau khi đăng ký
        Auth::login($user);

        return redirect()->route('guest.index')->with('success', 'Đăng ký thành công! Chào mừng bạn.');
    }
}