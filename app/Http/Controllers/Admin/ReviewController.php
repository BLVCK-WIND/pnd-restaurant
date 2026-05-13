<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $reviews = Review::query()
            ->with(['user', 'booking.table.area'])
            ->where('status', $status)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->filled('rating'), function ($q) use ($request) {
                $q->where('rating', $request->rating);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'status'));
    }

    public function approve(Review $review)
    {
        $review->update([
            'status'      => 'approved',
            'approved_by' => Auth::user()->id,
        ]);

        return back()->with('success', 'Đã duyệt review');
    }

    public function reject(Review $review)
    {
        $review->update([
            'status'      => 'rejected',
            'approved_by' => Auth::user()->id,
        ]);

        return back()->with('error', 'Đã từ chối review');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Đã xoá review');
    }
}
