<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'booking_id',
        'table_id',
        'staff_id',
        'status',
        'note',
    ];
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Tính tổng tiền order
    public function getTotalAttribute(): int
    {
        return $this->orderItems->sum(fn($item) => $item->subtotal);    
    }

    public function scopeOpen($query){
        return $query->where('status', 'open');
    }

    public function scopeOfDate($query, $date){
        return $query->whereDate('created_at', $date);
    }
}
