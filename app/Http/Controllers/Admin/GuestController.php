<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'guest');

        // Tìm kiếm theo tên hoặc email
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Lọc theo ngày tạo
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Lọc theo trạng thái
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $guests = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.guests.index', compact('guests'));
    }

    public function show(User $guest)
    {
        $this->authorize('viewGuest', $guest);

        // Load thêm booking history
        $guest->load(['bookings' => fn($q) => $q->latest()->limit(5)]);

        return view('admin.guests.show', compact('guest'));
    }

    public function toggleActive(User $guest)
    {
        $this->authorize('toggleGuest', $guest);

        $guest->update(['is_active' => !$guest->is_active]);

        $msg = $guest->is_active ? 'Đã kích hoạt tài khoản' : 'Đã vô hiệu hoá tài khoản';

        return redirect()->route('admin.guests.index')->with('success', $msg);
    }

    public function destroy(User $guest)
    {
        $this->authorize('deleteGuest', $guest);

        $guest->delete();

        return redirect()->route('admin.guests.index')->with('success', 'Xoá khách hàng thành công');
    }
}