<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Review;
use App\Models\StaffSchedule;
use Illuminate\Support\Carbon;

class DashboardService
{
    public function getDashboardData(): array
    {
        $today = Carbon::today();

        return [
            'revenueToday'     => $this->getRevenueToday($today),
            'ordersToday'      => $this->getOrdersToday($today),
            'bookingsToday'    => $this->getBookingsToday($today),
            'reviewsPending'   => $this->getReviewsPending(),
            'last7Days'        => $this->getLast7DaysRevenue($today),
            'topMenuItems'     => $this->getTopMenuItems($today),
            'staffToday'       => $this->getStaffToday($today),
            'bookings'         => $this->getPendingBookings($today),
            'last4Weeks'       => $this->getLast4WeeksRevenue($today),
            'revenueThisMonth' => $this->getRevenueThisMonth($today),
            'revenueLastMonth' => $this->getRevenueLastMonth($today),
            'revenueGrowth'    => $this->getRevenueGrowth($today),
        ];
    }

    private function getRevenueToday(Carbon $today): int
    {
        $key = 'dashboard:revenue_today:' . $today->format('Y-m-d');
        return cache()->remember($key, 300, function() use ($today){
                return Payment::whereDate('paid_at', $today)->sum('amount');
        });
    }

    private function getOrdersToday(Carbon $today): int
    {
        return Order::ofDate($today)
            ->whereIn('status', ['open', 'paid'])
            ->count();
    }

    private function getBookingsToday(Carbon $today): int
    {
        $key = 'dashboard:bookings_today:' . $today->format('Y-m-d');
        return cache()->remember($key, 300, function() use ($today) {
            return Booking::ofDate($today)->active()->count();
        });
    }

    private function getReviewsPending(): int
    {
        return cache()->remember('dashboard:reviews_pending', 300, function(){
            return Review::where('status','pending')->count();
        });
    }

    private function getLast7DaysRevenue(Carbon $today): \Illuminate\Support\Collection
    {
        $key = 'dashboard:last_7_days:' . $today->format('Y-m-d');

        return cache()->remember($key, 300, function () use ($today) {
            return collect(range(6, 0))->map(function ($i) use ($today) {
                $date = $today->copy()->subDays($i);
                return [
                    'date'  => $date->format('d/m'),
                    'total' => Payment::whereDate('paid_at', $date)->sum('amount'),
                ];
            });
        });
    }

    private function getTopMenuItems(Carbon $today): \Illuminate\Support\Collection
    {
        $key = 'dashboard:top_menu_items:' . $today->format('Y-m-d');
        return cache()->remember($key,300, function() use ($today){
            return OrderItem::select('menu_item_id')
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
            ]);
        });
    }

    private function getStaffToday(Carbon $today): \Illuminate\Support\Collection
    {
        return StaffSchedule::with(['user', 'shift'])
            ->whereDate('work_date', $today)
            ->orderBy('shift_id')
            ->get()
            ->groupBy('shift.name');
    }

    private function getPendingBookings(Carbon $today): \Illuminate\Support\Collection
    {
        return Booking::with(['user', 'table.area', 'staff'])
            ->ofDate($today)
            ->ofStatus('pending')
            ->orderBy('start_time')
            ->get();
    }

    private function getLast4WeeksRevenue(Carbon $today): \Illuminate\Support\Collection
    {
        $key = 'dashboard:last_4_weeks:' . $today->format('Y-W');

        return cache()->remember($key, 300, function () use ($today) {
            return collect(range(3, 0))->map(function ($i) use ($today) {
                $start = $today->copy()->startOfWeek()->subWeeks($i);
                $end   = $start->copy()->endOfWeek();
                return [
                    'date'  => 'Tuần ' . (4 - $i),
                    'total' => Payment::whereBetween('paid_at', [$start, $end])->sum('amount'),
                ];
            });
        });
    }

    private function getRevenueThisMonth(Carbon $today): int
    {
        $key = 'dashboard:revenue_month:' . $today->format('Y-m');

        return cache()->remember($key, 300, function () use ($today) {
            return Payment::whereMonth('paid_at', $today->month)
                ->whereYear('paid_at', $today->year)
                ->sum('amount');
        });
    }
    private function getRevenueLastMonth(Carbon $today): int
    {
        $lastMonth = $today->copy()->subMonth();
        $key = 'dashboard:revenue_month:' . $lastMonth->format('Y-m');

        return cache()->remember($key, 300, function () use ($lastMonth) {
            return Payment::whereMonth('paid_at', $lastMonth->month)
                ->whereYear('paid_at', $lastMonth->year)
                ->sum('amount');
        });
    }
    private function getRevenueGrowth(Carbon $today): ?float
    {
        $thisMonth = $this->getRevenueThisMonth($today);
        $lastMonth = $this->getRevenueLastMonth($today);

        return $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
            : null;
    }
}