<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $weekStart = $request->filled('week')
            ? Carbon::parse($request->week)->startOfWeek()
            : Carbon::now()->startOfWeek();

        $weekEnd = $weekStart->copy()->endOfWeek();

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = $weekStart->copy()->addDays($i);
        }

        $staffs = User::where('role', 'staff')->orderBy('name')->get();

        // Fix: cast work_date về string Y-m-d trước khi groupBy
        // tránh lỗi key kiểu "2025-05-08 00:00:00" không khớp với format('Y-m-d')
        $schedules = StaffSchedule::with('shift')
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->get()
            ->groupBy([
                'user_id',
                fn($s) => Carbon::parse($s->work_date)->format('Y-m-d'),
            ]);

        return view('staff.schedules.index', compact(
            'staffs', 'days', 'schedules',
            'weekStart', 'weekEnd'
        ));
    }
}