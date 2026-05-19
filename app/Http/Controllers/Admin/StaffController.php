<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role','staff');

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

        $staffs = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.staffs.index', compact('staffs'));
    }

    public function create()
    {
        return view('admin.staffs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6|confirmed',
            'role'      => 'required|in:admin,staff',
            'phone'     => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = $request->has('is_active');

        User::create($data);

        return redirect()->route('admin.staffs.index')->with('success', 'Thêm nhân viên thành công');
    }

    public function edit(User $staff)
    {
        // Đảm bảo chỉ edit được admin/staff
        abort_if(!in_array($staff->role, ['admin', 'staff']), 404);

        return view('admin.staffs.edit', compact('staff'));
    }

    public function update(Request $request, User $staff)
    {
        abort_if(!in_array($staff->role, ['admin', 'staff']), 404);

        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $staff->id,
            'password'  => 'nullable|min:6|confirmed',
            'role'      => 'required|in:admin,staff',
            'phone'     => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        // Chỉ update password nếu có nhập
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $data['is_active'] = $request->has('is_active');

        $staff->update($data);

        return redirect()->route('admin.staffs.index')->with('success', 'Cập nhật nhân viên thành công');
    }

    public function destroy(User $staff)
    {
        $this->authorize('deleteStaff', $staff);

        $staff->delete();

        return redirect()->route('admin.staffs.index')->with('success', 'Xoá nhân viên thành công');
    }
    public function toggle(User $staff)
    {
        $this->authorize('toggleStaff', $staff);
        $staff->update(['is_active' => !$staff->is_active]);

        $msg = $staff->is_active ? 'Đã kích hoạt tài khoản' : 'Đã vô hiệu hoá tài khoản';

        return redirect()->route('admin.staffs.index')->with('success', $msg);
    }
}