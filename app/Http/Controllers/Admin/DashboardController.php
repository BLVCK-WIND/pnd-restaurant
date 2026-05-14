<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Review;
use App\Models\StaffSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // ══════════════════════════════════════════
        // HÀNG 1 — 4 thẻ số liệu nhanh
        // ══════════════════════════════════════════

        // 💰 Doanh thu hôm nay
        $revenueToday = Payment::whereDate('paid_at', $today)->sum('amount');

        // 🧾 Order hôm nay (chỉ đếm open + paid, bỏ cancelled)
        $ordersToday = Order::whereDate('created_at', $today)
            ->whereIn('status', ['open', 'paid'])
            ->count();

        // 📅 Booking hôm nay
        $bookingsToday = Booking::whereDate('start_time', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        // ⭐ Review chờ duyệt
        $reviewsPending = Review::where('status', 'pending')->count();

        // ══════════════════════════════════════════
        // BIỂU ĐỒ — Doanh thu 7 ngày gần nhất
        // ══════════════════════════════════════════
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $last7Days->push([
                'date'  => $date->format('d/m'),
                'total' => Payment::whereDate('paid_at', $date)->sum('amount'),
            ]);
        }

        // ══════════════════════════════════════════
        // TOP 5 MÓN BÁN CHẠY HÔM NAY
        // ══════════════════════════════════════════
        $topMenuItems = OrderItem::select('menu_item_id')
            ->selectRaw('SUM(quantity) as sold_today')
            ->whereHas('order', fn($q) =>
                $q->whereDate('created_at', $today)
                ->where('status', 'paid')
            )
            ->groupBy('menu_item_id')
            ->orderByDesc('sold_today')
            ->limit(5)
            ->with('menuItem')
            ->get()
            ->map(fn($item) => (object)[
                'name'       => $item->menuItem->name,
                'sold_today' => $item->sold_today,
                'iteration'  => null, // loop->iteration tự xử lý trong blade
            ]);

        // ══════════════════════════════════════════
        // NHÂN VIÊN LÀM VIỆC HÔM NAY
        // ══════════════════════════════════════════
        $staffToday = StaffSchedule::with(['user', 'shift'])
            ->whereDate('work_date', $today)
            ->orderBy('shift_id')
            ->get()
            ->groupBy('shift.name'); // Group theo ca: Ca sáng / Ca chiều / Ca tối

        // ══════════════════════════════════════════
        // BOOKING HÔM NAY — sắp xếp theo giờ đến
        // ══════════════════════════════════════════
        $bookings = Booking::with(['user', 'table.area', 'staff'])
            ->where('status','pending')
            ->whereDate('start_time', $today)
            ->orderBy('start_time')
            ->get();

        $last4Weeks = collect();
        for ($i = 3; $i >= 0; $i--) {
            $start = $today->copy()->startOfWeek()->subWeeks($i);
            $end   = $start->copy()->endOfWeek();
            $last4Weeks->push([
                'date'  => 'Tuần ' . (4 - $i),
                'total' => Payment::whereBetween('paid_at', [$start, $end])->sum('amount'),
            ]);
        }

        // Doanh thu tháng này & tháng trước
        $revenueThisMonth = Payment::whereMonth('paid_at', $today->month)
            ->whereYear('paid_at', $today->year)
            ->sum('amount');

        $revenueLastMonth = Payment::whereMonth('paid_at', $today->copy()->subMonth()->month)
            ->whereYear('paid_at', $today->copy()->subMonth()->year)
            ->sum('amount');

        $revenueGrowth = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : null;

        return view('admin.dashboard', compact(
            'revenueToday',
            'ordersToday',
            'bookingsToday',
            'reviewsPending',
            'last7Days',
            'topMenuItems',
            'staffToday',
            'bookings',
            'last4Weeks',
            'revenueThisMonth',
            'revenueGrowth',
        ));
    }
}
