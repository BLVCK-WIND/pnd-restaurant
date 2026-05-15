<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RevenueService
{
    private function calcPercent(int|float $current, int|float $previous): array{
        if($previous==0){
            return ['value' => $current>0 ? 100: 0, 'up' => $current > 0];
        }
        $percent = (($current-$previous)/$previous)*100;
        return ['value' => round(abs($percent),1), 'up' => $percent >=0];
    }

    public function getMonthlyData(Request $request): array
    {
        $today = Carbon::today();

        // Tháng đang xem — mặc định tháng hiện tại
        $currentMonth = $request->filled('month')
            ? Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()
            : $today->copy()->startOfMonth();

        $prevMonth = $currentMonth->copy()->subMonth();
        $nextMonth = $currentMonth->copy()->addMonth();
        $canGoNext = $nextMonth->lte($today->copy()->startOfMonth());

        return [
            'mode'             => 'month',
            'today'            => $today,
            'currentMonth'     => $currentMonth,
            'prevMonth'        => $prevMonth,
            'nextMonth'        => $nextMonth,
            'canGoNext'        => $canGoNext,
            'revenueThisMonth' => $this->getMonthRevenue($currentMonth),
            'revenueLastMonth' => $this->getMonthRevenue($prevMonth),
            'changeMonth'      => $this->calcPercent(
                                    $this->getMonthRevenue($currentMonth),
                                    $this->getMonthRevenue($prevMonth)
                                ),
            'dailyRevenue'     => $this->getDailyRevenue($currentMonth),
            'paymentMethods'   => $this->getPaymentMethods($currentMonth),
        ];
    }

    private function getMonthRevenue(Carbon $month): int
    {
        return Payment::whereMonth('paid_at', $month->month)
            ->whereYear('paid_at', $month->year)
            ->sum('amount');
    }

    private function getDailyRevenue(Carbon $month): \Illuminate\Support\Collection
    {
        return Payment::selectRaw('DATE(paid_at) as date, SUM(amount) as total, COUNT(*) as count')
            ->whereMonth('paid_at', $month->month)
            ->whereYear('paid_at', $month->year)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
    private function getPaymentMethods(Carbon $month): \Illuminate\Support\Collection
    {
        return Payment::selectRaw('method, COUNT(*) as count, SUM(amount) as total')
            ->whereMonth('paid_at', $month->month)
            ->whereYear('paid_at', $month->year)
            ->groupBy('method')
            ->get();
    }

    public function getWeeklyData(Request $request): array
    {
        $today = Carbon::today();

        $weekStart = $request->filled('week')
            ? Carbon::parse($request->week)->startOfWeek()
            : $today->copy()->startOfWeek();

        $weekEnd  = $weekStart->copy()->endOfWeek();
        $prevWeek = $weekStart->copy()->subWeek();
        $nextWeek = $weekStart->copy()->addWeek();
        $canGoNext = $nextWeek->lte($today->copy()->startOfWeek());

        return [
            'mode'            => 'week',
            'today'           => $today,
            'weekStart'       => $weekStart,
            'weekEnd'         => $weekEnd,
            'prevWeek'        => $prevWeek,
            'nextWeek'        => $nextWeek,
            'canGoNext'       => $canGoNext,
            'revenueThisWeek' => $this->getWeekRevenue($weekStart, $weekEnd),
            'revenueLastWeek' => $this->getWeekRevenue(
                                    $prevWeek,
                                    $prevWeek->copy()->endOfWeek()
                                ),
            'changeWeek'      => $this->calcPercent(
                                    $this->getWeekRevenue($weekStart, $weekEnd),
                                    $this->getWeekRevenue($prevWeek, $prevWeek->copy()->endOfWeek())
                                ),
            'dailyRevenue'    => $this->getWeekDailyRevenue($weekStart, $weekEnd),
            'paymentMethods'  => $this->getWeekPaymentMethods($weekStart, $weekEnd),
        ];
    }

    private function getWeekRevenue(Carbon $start, Carbon $end): int
    {
        return Payment::whereBetween('paid_at', [
            $start->copy()->startOfDay(),
            $end->copy()->endOfDay(),
        ])->sum('amount');
    }

    private function getWeekDailyRevenue(Carbon $start, Carbon $end): \Illuminate\Support\Collection
    {
        return Payment::selectRaw('DATE(paid_at) as date, SUM(amount) as total, COUNT(*) as count')
            ->whereBetween('paid_at', [
                $start->copy()->startOfDay(),
                $end->copy()->endOfDay(),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getWeekPaymentMethods(Carbon $start, Carbon $end): \Illuminate\Support\Collection
    {
        return Payment::selectRaw('method, COUNT(*) as count, SUM(amount) as total')
            ->whereBetween('paid_at', [
                $start->copy()->startOfDay(),
                $end->copy()->endOfDay(),
            ])
            ->groupBy('method')
            ->get();
    }
}