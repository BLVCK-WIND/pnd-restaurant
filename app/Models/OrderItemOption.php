<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemOption extends Model
{
    protected $fillable = [
        'order_item_id',
        'option_value_id',
        'extra_price',
    ];

    public function orderItem(){
        return $this->belongsTo(OrderItem::class);
    }

    public function optionValue(){
        return $this->belongsTo(OptionValue::class);
    }
}
