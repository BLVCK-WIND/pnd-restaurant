<?php

namespace App\Observers;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingObserver
{


    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Chỉ ghi log khi status thay đổi
        if ($booking->wasChanged('status')) {
            $booking->logs()->create([
                'staff_id' => Auth::id(),
                'action'   => $booking->status,
                'note'     => null,
            ]);
        }
    }

}
