<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Table;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     * Quản lý theo ngày, giới hạn tuần trước – tuần này – tuần sau
     */
    public function index(Request $request)
    {
        $today     = Carbon::today();
        $minDate   = $today->copy()->startOfWeek()->subWeek(); // Đầu tuần trước
        $maxDate   = $today->copy()->endOfWeek()->addWeek();   // Cuối tuần sau

        // Ngày đang xem — mặc định hôm nay
        $selectedDate = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : $today->copy();

        // Giới hạn không ra ngoài phạm vi cho phép
        if ($selectedDate->lt($minDate)) $selectedDate = $minDate->copy();
        if ($selectedDate->gt($maxDate)) $selectedDate = $maxDate->copy();

        $prevDate = $selectedDate->copy()->subDay();
        $nextDate = $selectedDate->copy()->addDay();

        $canGoPrev = $prevDate->gte($minDate);
        $canGoNext = $nextDate->lte($maxDate);

        // Xác định quyền thao tác theo ngày
        $isToday  = $selectedDate->isToday();
        $isFuture = $selectedDate->isFuture();

        // Mặc định tab = pending (nếu không có query status)
        $activeStatus = $request->input('status', 'pending');

        // Đếm số lượng theo từng status trong ngày (để hiện badge số)
        $statusCounts = Booking::ofDate($selectedDate)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $bookings = Booking::query()
            ->with(['user', 'table.area', 'staff'])
            ->ofDate($selectedDate)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('guest_name', 'like', '%' . $request->search . '%')
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $request->search . '%'));
                });
            })
            ->ofStatus($activeStatus)
            ->orderBy('start_time', 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('manage.bookings.index', compact(
            'bookings',
            'selectedDate',
            'prevDate',
            'nextDate',
            'canGoPrev',
            'canGoNext',
            'isToday',
            'isFuture',
            'today',
            'minDate',
            'maxDate',
            'activeStatus',
            'statusCounts',
        ));
    }

    public function confirm(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking này không thể xác nhận');
        }
        try{
            DB::transaction(function () use ($booking) {
                $booking->update([
                    'status'       => 'confirmed',
                    'confirmed_at' => now(),
                    'staff_id'     => Auth::user()->id,
                ]);

                $booking->addLog('confirmed', Auth::user()->id);
                Order::create([
                    'booking_id' => $booking->id,
                    'table_id'   => $booking->table_id,
                    'staff_id'   => Auth::user()->id,
                    'status'     => 'open',
                ]);
            });
        }catch(\Exception $e){
            return back()->with('error', 'Đã có lỗi xảy ra, vui lòng thử lại');
        }
        return redirect()
            ->route('manage.bookings.index', ['date' => $booking->start_time->format('Y-m-d')])
            ->with('success', 'Xác nhận khách đến thành công — Order đã được tạo');
    }

    public function complete(Booking $booking)
    {
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Booking chưa được xác nhận');
        }
        try{
            DB::transaction(function () use ($booking) {
                $booking->update(['status' => 'completed']);
                $booking->addLog('completed', Auth::user()->id);
            });
        }catch(\Exception $e){
            return back()->with('error', 'Đã xảy ra lỗi, vui lòng thử lại');
        }
        return redirect()
            ->route('manage.bookings.index', ['date' => $booking->start_time->format('Y-m-d')])
            ->with('success', 'Khách đã dùng bữa xong và trả bàn');
    }

    public function cancel(Booking $booking)
    {
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Không thể hủy đơn này');
        }
        try{
            DB::transaction(function () use ($booking) {
                $booking->update(['status' => 'cancelled']);
                $booking->addLog('cancelled', Auth::user()->id);
            });
        }catch(\Exception $e){
            return back()->with('error', 'Đã xảy ra lỗi, vui lòng thử lại');
        }
        return redirect()
            ->route('manage.bookings.index', ['date' => $booking->start_time->format('Y-m-d')])
            ->with('success', 'Hủy đơn thành công');
    }

    public function create()
    {
        $now     = Carbon::now();
        $endTime = Carbon::now()->addHours(3);

        $tables = Table::active()
            ->whereDoesntHave('bookings', function ($query) use ($now, $endTime) {
                $query->active()->conflictsWith($now, $endTime);
            })
            ->with('area')
            ->get();

        return view('manage.bookings.create', [
            'tables'     => $tables,
            'start_time' => $now->format('Y-m-d H:i:s'),
            'end_time'   => $endTime->format('Y-m-d H:i:s'),
        ]);
    }

    public function store(StoreBookingRequest $request)
    {
        $data = $request->validated();
        $isConflict = Booking::forTable($data['table_id'])
            ->active()->conflictsWith($data['start_time'], $data['end_time'])
            ->exists();

        if ($isConflict) {
            return back()->with('error', 'Bàn này vừa được đặt, vui lòng chọn bàn khác');
        }
        try{
            DB::transaction(function () use ($data) {
                $booking = Booking::create([
                    'user_id'      => null,
                    'table_id'     => $data['table_id'],
                    'guest_name'   => $data['guest_name'] ?? 'Khách vãng lai',
                    'guest_phone'  => $data['guest_phone'] ?? '',
                    'guest_count'  => $data['guest_count'],
                    'start_time'   => $data['start_time'],
                    'end_time'     => $data['end_time'],
                    'status'       => 'confirmed',
                    'confirmed_at' => now(),
                    'staff_id'     => Auth::user()->id,
                    'note'         => $data['note'] ?? null,
                ]);

                $booking->addLog('confirmed', Auth::user()->id, 'Walk-in');
                Order::create([
                    'booking_id' => $booking->id,
                    'table_id'   => $booking->table_id,
                    'staff_id'   => Auth::user()->id,
                    'status'     => 'open',
                ]);
            });
        }catch(\Exception $e){
            return back()->with('error', 'Đã xảy ra lỗi, vui lòng thử lại');
        }

        return redirect()
            ->route('manage.bookings.index')
            ->with('success', 'Tạo đơn walk-in thành công');
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'table.area', 'staff', 'logs.staff']);
        return view('manage.bookings.show', compact('booking'));
    }
}