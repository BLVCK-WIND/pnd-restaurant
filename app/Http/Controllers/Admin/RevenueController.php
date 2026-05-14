<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // ── Người dùng chọn chế độ xem: day | week | month ──
        $mode = $request->input('mode', 'month'); // mặc định xem theo tháng

        // ══════════════════════════════════════════
        // MODE: THÁNG
        // ══════════════════════════════════════════
        if ($mode === 'month') {

            // Tháng đang xem — mặc định tháng hiện tại
            $currentMonth = $request->filled('month')
                ? Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()
                : $today->copy()->startOfMonth();

            $prevMonth = $currentMonth->copy()->subMonth();
            $nextMonth = $currentMonth->copy()->addMonth();
            $canGoNext = $nextMonth->lte($today->copy()->startOfMonth());

            // Doanh thu tháng đang xem
            $revenueThisMonth = Payment::whereMonth('paid_at', $currentMonth->month)
                ->whereYear('paid_at', $currentMonth->year)
                ->sum('amount');

            // Doanh thu tháng trước (để tính %)
            $revenueLastMonth = Payment::whereMonth('paid_at', $prevMonth->month)
                ->whereYear('paid_at', $prevMonth->year)
                ->sum('amount');

            $changeMonth = $this->calcPercent($revenueThisMonth, $revenueLastMonth);

            // Doanh thu từng ngày trong tháng
            $dailyRevenue = Payment::selectRaw('DATE(paid_at) as date, SUM(amount) as total, COUNT(*) as count')
                ->whereMonth('paid_at', $currentMonth->month)
                ->whereYear('paid_at', $currentMonth->year)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Phương thức thanh toán trong tháng
            $paymentMethods = Payment::selectRaw('method, COUNT(*) as count, SUM(amount) as total')
                ->whereMonth('paid_at', $currentMonth->month)
                ->whereYear('paid_at', $currentMonth->year)
                ->groupBy('method')
                ->get();

            return view('admin.revenue.index', compact(
                'mode',
                'today',
                'currentMonth',
                'prevMonth',
                'nextMonth',
                'canGoNext',
                'revenueThisMonth',
                'revenueLastMonth',
                'changeMonth',
                'dailyRevenue',
                'paymentMethods',
            ));
        }

        // ══════════════════════════════════════════
        // MODE: TUẦN
        // ══════════════════════════════════════════
        if ($mode === 'week') {

            // Tuần đang xem — mặc định tuần hiện tại
            $weekStart = $request->filled('week')
                ? Carbon::parse($request->week)->startOfWeek()
                : $today->copy()->startOfWeek();

            $weekEnd  = $weekStart->copy()->endOfWeek();
            $prevWeek = $weekStart->copy()->subWeek();
            $nextWeek = $weekStart->copy()->addWeek();
            $canGoNext = $nextWeek->lte($today->copy()->startOfWeek());

            // Doanh thu tuần đang xem
            $revenueThisWeek = Payment::whereBetween('paid_at', [
                $weekStart->copy()->startOfDay(),
                $weekEnd->copy()->endOfDay(),
            ])->sum('amount');

            // Doanh thu tuần trước (để tính %)
            $revenueLastWeek = Payment::whereBetween('paid_at', [
                $prevWeek->copy()->startOfDay(),
                $prevWeek->copy()->endOfWeek()->endOfDay(),
            ])->sum('amount');

            $changeWeek = $this->calcPercent($revenueThisWeek, $revenueLastWeek);

            // Doanh thu từng ngày trong tuần
            $dailyRevenue = Payment::selectRaw('DATE(paid_at) as date, SUM(amount) as total, COUNT(*) as count')
                ->whereBetween('paid_at', [
                    $weekStart->copy()->startOfDay(),
                    $weekEnd->copy()->endOfDay(),
                ])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Phương thức thanh toán trong tuần
            $paymentMethods = Payment::selectRaw('method, COUNT(*) as count, SUM(amount) as total')
                ->whereBetween('paid_at', [
                    $weekStart->copy()->startOfDay(),
                    $weekEnd->copy()->endOfDay(),
                ])
                ->groupBy('method')
                ->get();

            return view('admin.revenue.index', compact(
                'mode',
                'today',
                'weekStart',
                'weekEnd',
                'prevWeek',
                'nextWeek',
                'canGoNext',
                'revenueThisWeek',
                'revenueLastWeek',
                'changeWeek',
                'dailyRevenue',
                'paymentMethods',
            ));
        }
    }

    private function calcPercent(int|float $current, int|float $previous): array
    {
        if ($previous == 0) {
            return ['value' => $current > 0 ? 100 : 0, 'up' => $current > 0];
        }
        $percent = (($current - $previous) / $previous) * 100;
        return ['value' => round(abs($percent), 1), 'up' => $percent >= 0];
    }
}
