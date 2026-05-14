<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Order;
use App\Models\StaffSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class StaffDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $user  = Auth::user();
 
        // ══════════════════════════════════════════
        // CA LÀM CỦA NHÂN VIÊN HÔM NAY (có thể nhiều ca)
        // ══════════════════════════════════════════
        $mySchedules = StaffSchedule::with('shift')
            ->where('user_id', $user->id)
            ->whereDate('work_date', $today)
            ->orderBy('shift_id')
            ->get()
            ->map(function ($schedule) {
                $now = Carbon::now();
 
                // Ghép ngày hôm nay + giờ shift — tránh Carbon parse sai ngày
                $shiftStart = Carbon::today()->setTimeFromTimeString($schedule->shift->start_time);
                $shiftEnd   = Carbon::today()->setTimeFromTimeString($schedule->shift->end_time);
 
                $isActive = $now->between($shiftStart, $shiftEnd);
                $isPast   = $now->gt($shiftEnd);
 
                return [
                    'name'       => $schedule->shift->name,
                    'start'      => $shiftStart->format('H:i'),
                    'end'        => $shiftEnd->format('H:i'),
                    'is_active'  => $isActive,
                    'is_past'    => $isPast,
                    'diff_human' => $isActive
                        ? $now->diffForHumans($shiftEnd,   \Carbon\CarbonInterface::DIFF_ABSOLUTE, true, 2)
                        : ($isPast
                            ? null
                            : $now->diffForHumans($shiftStart, \Carbon\CarbonInterface::DIFF_ABSOLUTE, true, 2)),
                ];
            });
 
        // ══════════════════════════════════════════
        // 3 STAT CARDS
        // ══════════════════════════════════════════
        $bookingsPending   = Booking::whereDate('start_time', $today)->where('status', 'pending')->count();
        $bookingsConfirmed = Booking::whereDate('start_time', $today)->where('status', 'confirmed')->count();
        $ordersOpen        = Order::whereDate('created_at', $today)->where('status', 'open')->count();
 
        // ══════════════════════════════════════════
        // BOOKING PENDING — sắp xếp theo giờ đến
        // ══════════════════════════════════════════
        $pendingBookings = Booking::with(['user', 'table.area'])
            ->whereDate('start_time', $today)
            ->where('status', 'pending')
            ->orderBy('start_time')
            ->get();
 
        // ══════════════════════════════════════════
        // ORDER ĐANG MỞ HÔM NAY
        // ══════════════════════════════════════════
        $openOrders = Order::with(['table.area', 'booking', 'orderItems'])
            ->whereDate('created_at', $today)
            ->where('status', 'open')
            ->latest()
            ->get();
 
        // ══════════════════════════════════════════
        // LỊCH CA TOÀN NHÂN VIÊN HÔM NAY
        // ══════════════════════════════════════════
        $allSchedulesToday = StaffSchedule::with(['user', 'shift'])
            ->whereDate('work_date', $today)
            ->orderBy('shift_id')
            ->get()
            ->groupBy('shift.name');
 
        return view('staff.dashboard', compact(
            'mySchedules',
            'bookingsPending',
            'bookingsConfirmed',
            'ordersOpen',
            'pendingBookings',
            'openOrders',
            'allSchedulesToday',
        ));
    }
}
