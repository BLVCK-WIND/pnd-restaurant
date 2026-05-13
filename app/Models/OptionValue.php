<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptionValue extends Model
{
    protected $fillable = [
        'option_group_id',
        'name',
        'extra_price',
    ];

    public function optionGroup(){
        return $this->belongsTo(OptionGroup::class);
    }
}
