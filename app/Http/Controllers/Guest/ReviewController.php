<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        $booking = Booking::findOrFail($data['booking_id']);

        // Kiểm tra booking có phải của mình không
        if ($booking->user_id !== Auth::user()->id) {
            return response()->json(['error' => 'Không có quyền review booking này'], 403);
        }

        // Kiểm tra booking đã completed chưa
        if ($booking->status !== 'completed') {
            return response()->json(['error' => 'Booking chưa hoàn tất'], 400);
        }

        // Kiểm tra đã review chưa
        if ($booking->review) {
            return response()->json(['error' => 'Bạn đã review booking này rồi'], 400);
        }

        // Tạo review
        Review::create([
            'user_id'    => Auth::user()->id,
            'booking_id' => $data['booking_id'],
            'rating'     => $data['rating'],
            'comment'    => $data['comment'] ?? null,
            'status'     => 'pending',
        ]);

        return response()->json(['message' => 'Gửi review thành công']);
    }
}
