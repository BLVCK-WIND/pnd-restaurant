<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if($user->role === 'admin'){
            return true;
        }
        return null;
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->role === 'guest'
            && $user->id === $booking->user_id  // ← thêm dòng này!
            && $booking->status === 'pending';
    }

    public function confirm(User $user, Booking $booking): bool
    {
        return $user->role === 'staff' && $booking->status === 'pending';
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $user->role === 'staff' && $booking->status === 'pending';
    }

    public function complete(User $user, Booking $booking): bool
    {
        return $user->role === 'staff' && $booking->status === 'confirmed';
    }
}
