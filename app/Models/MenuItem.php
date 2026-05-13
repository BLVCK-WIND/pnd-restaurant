<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'status',
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }

    public function optionGroups(){
        return $this->belongsToMany(OptionGroup::class);
    }

    public function getAllOptionGroups(){
        return $this->optionGroups->merge($this->category->optionGroups)->unique('id')->load('optionValues');
    }
}
