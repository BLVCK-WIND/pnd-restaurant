<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'table_id',
        'staff_id',
        'guest_name',
        'guest_phone',
        'guest_count',
        'start_time',
        'end_time',
        'status',
        'note',
        'confirmed_at',
    ];

    protected $casts = [
        'start_time'   => 'datetime',
        'end_time'     => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function table(){
        return $this->belongsTo(Table::class);
    }
    public function staff(){
        return $this->belongsTo(User::class, 'staff_id');
    }
    public function review()
    {
        return $this->hasOne(Review::class);
    }
    public function order()
    {
        return $this->hasOne(Order::class);
    }
    public function logs()
    {
        return $this->hasMany(BookingLog::class)->orderBy('created_at', 'asc');
    }
    public function addLog(string $action, int $staffId, ?string $note = null): void
    {
        $this->logs()->create([
            'staff_id' => $staffId,
            'action'   => $action,
            'note'     => $note,
        ]);
    }
}
