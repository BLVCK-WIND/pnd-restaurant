<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\StaffSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $weekStart = $request->filled('week')
            ? Carbon::parse($request->week)->startOfWeek()
            : Carbon::now()->startOfWeek();

        $weekEnd = $weekStart->copy()->endOfWeek();

        // Kiểm tra tuần hiện tại hay quá khứ
        $isPastWeek = $weekEnd->isPast();

        // 7 ngày trong tuần
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = $weekStart->copy()->addDays($i);
        }

        // Danh sách staff
        $staffs = User::where('role', 'staff')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name')
            ->get();

        // Tất cả ca làm
        $shifts = Shift::orderBy('start_time')->get();

        // Lịch trong tuần — group theo user_id + work_date + shift_id
        $schedules = StaffSchedule::with('shift')
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->get()
            ->groupBy([
                'user_id',
                fn($s) => Carbon::parse($s->work_date)->format('Y-m-d'),
            ]);

        return view('admin.schedules.index', compact(
            'staffs', 'shifts', 'days', 'schedules',
            'weekStart', 'weekEnd', 'isPastWeek'
        ));
    }

    public function toggle(Request $request)
    {
        // Không cho sửa tuần quá khứ
        $workDate = Carbon::parse($request->work_date);
        if ($workDate->endOfWeek()->isPast()) {
            return response()->json(['error' => 'Không thể sửa lịch tuần đã qua'], 403);
        }

        $data = $request->validate([
            'user_id'   => 'required|exists:users,id',
            'shift_id'  => 'required|exists:shifts,id',
            'work_date' => 'required|date',
        ]);

        $existing = StaffSchedule::where('user_id', $data['user_id'])
            ->where('shift_id', $data['shift_id'])
            ->where('work_date', $data['work_date'])
            ->first();

        if ($existing) {
            $existing->delete();
            $action = 'removed';
        } else {
            StaffSchedule::create($data);
            $action = 'added';
        }

        // Đếm tổng ca trong tuần
        $weekStart = Carbon::parse($data['work_date'])->startOfWeek();
        $weekEnd   = $weekStart->copy()->endOfWeek();

        $totalShifts = StaffSchedule::where('user_id', $data['user_id'])
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->count();

        return response()->json([
            'action'       => $action,
            'total_shifts' => $totalShifts,
        ]);
    }
}