<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptionGroup extends Model
{
    protected $fillable = [
        'name',
        'is_required',
        'is_multiple',
    ];

    public function optionValues(){
        return $this->hasMany(OptionValue::class);
    }

    public function categories(){
        return $this->belongsToMany(Category::class);
    }

    public function menuItems(){
        return $this->belongsToMany(MenuItem::class);
    }
}
