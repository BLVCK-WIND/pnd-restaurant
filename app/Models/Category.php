<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'sort_order',
        'is_active',
    ];

    public function menuItems(){
        return $this->hasMany(MenuItem::class);
    }

    public function optionGroups(){
        return $this->belongsToMany(OptionGroup::class);
    }
}
