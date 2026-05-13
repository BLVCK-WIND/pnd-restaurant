<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Staff xử lý booking
    public function handledBookings()
    {
        return $this->hasMany(Booking::class, 'staff_id');
    }

    // Staff tạo order
    public function orders()
    {
        return $this->hasMany(Order::class, 'staff_id');
    }

    // Staff thu tiền
    public function payments()
    {
        return $this->hasMany(Payment::class, 'staff_id');
    }

    // Lịch làm việc
    public function schedules()
    {
        return $this->hasMany(StaffSchedule::class);
    }

    // Review của guest
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Booking logs
    public function bookingLogs()
    {
        return $this->hasMany(BookingLog::class, 'staff_id');
    }
}
