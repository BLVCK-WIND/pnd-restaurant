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
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $service
    ) {}

    public function index()
    {
        $data = $this->service->getDashboardData();
        return view('admin.dashboard', $data);
    }
}
