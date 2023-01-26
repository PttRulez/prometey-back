<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CurrencyRate;

class Currency extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function currency_rates()
    {
        return $this->hasMany(CurrencyRate::class);
    }

    public function getRate($year, $month)
    {
        if ($rate = $this->currency_rates()->where(['year' => $year, 'month' => $month])->first())
            return $rate->rate_to_usd;
        else
            return false;
    }
}
