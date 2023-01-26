<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NonGameProfitType extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function nonGameProfits()
    {
        $this->hasMany(NonGameProfit::class);
    }
}
