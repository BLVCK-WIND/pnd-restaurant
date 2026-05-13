<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    protected $fillable = [
        'area_id',
        'name',
        'capacity',
        'status',
    ];

    public function area(){
        return $this->belongsTo(Area::class);
    }

    public function bookings(){
        return $this->hasMany(Booking::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
