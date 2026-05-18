<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guest\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::ofUser(Auth::user()->id)
            ->with(['table.area','review'])
            ->latest()
            ->paginate(10);

        return view('guest.bookings.index', compact('bookings'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $tables = collect();
        if($request->filled(['date', 'start_time', 'guest_count'])){
            $startTime = Carbon::parse($request->date. ' ' .$request->start_time);
            if($startTime->isPast()){
                return back()->withErrors(['start_time'=>'Thời gian đặt bàn không được ở quá khứ']);
            }
            $endTime = $startTime->copy()->addHours(3);
            $tables = Table::active()
            ->where('capacity', '>=', $request->guest_count)
            ->whereDoesntHave('bookings', function($query) use ($endTime, $startTime){
                $query->active()->conflictsWith($startTime, $endTime);
            })
            ->with('area')
            ->get();
        }

        return view('guest.bookings.create', compact('tables'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $data = $request->validated();
        $table = Table::findOrFail($data['table_id']);
        if($table->status!=='active'){
            return back()->withErrors(['status'=>'Bàn này đang ngưng hoạt động']);
        }
        if($table->capacity < $data['guest_count']){
            return back()->withErrors(['count'=>'Sức chứa của bàn không đủ']);
        }

        $isConflict = Booking::forTable($data['table_id'])
        ->active()->conflictsWith($data['start_time'], $data['end_time'])
        ->exists();
        if($isConflict){
            return back()->withErrors(['table_id'=>'Bàn này hiện đang bận hoặc không khớp với thời gian mà bạn chọn']);
        }
        $data['user_id'] = Auth::user()->id;
        $data['status'] = 'pending';
        Booking::create($data);
        return redirect()->route('guest.bookings.index')->with('success', 'Bạn đã đặt bàn thành công');
    }

    public function destroy(Booking $booking)
    {
        // Bước 1 — Kiểm tra booking này có phải của mình không
        if ($booking->user_id !== Auth::user()->id) {
            abort(403, 'Bạn không có quyền huỷ booking này');
        }

        // Bước 2 — Chỉ huỷ được khi status = pending
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể huỷ booking đang chờ xác nhận');
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()
            ->route('guest.bookings.index')
            ->with('success', 'Huỷ đặt bàn thành công');
    }
}
