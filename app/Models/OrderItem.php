<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'menu_item_id',
        'quantity',
        'unit_price',
        'note',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function getSubtotalAttribute(): int
    {
        $optionPrice= $this->orderItemOptions->sum('extra_price');
        return ($optionPrice + $this->unit_price)*$this->quantity;
    }

    public function orderItemOptions(){
        return $this->hasMany(OrderItemOption::class);
    }

}
