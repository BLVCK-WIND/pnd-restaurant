<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\RevenueService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RevenueController extends Controller
{
    public function __construct(
        private RevenueService $service
    ) {}

    public function index(Request $request)
    {
        $mode = $request->input('mode', 'month');

        $data = match($mode) {
            'week'  => $this->service->getWeeklyData($request),
            default => $this->service->getMonthlyData($request),
        };

        return view('admin.revenue.index', $data);
    }
}
