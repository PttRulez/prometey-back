<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

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
        if ($this->id == 1)
            return 1;

        if ($rate = $this->currency_rates()->where(['year' => $year, 'month' => $month])->first()) {
            return $rate->rate_to_usd;
        } else {
            $padded_month = str_pad($month, 2, '0', STR_PAD_LEFT);
            $res = Http::get("https://www.alphavantage.co/query?function=FX_MONTHLY&from_symbol=USD&to_symbol=$this->name&apikey=CKRK9SRIXSSOO48D");

            if ($res->successful()) {
                $rates_array = $res->json();

                if (!array_key_exists('Time Series FX (Monthly)', $rates_array)) {
                    return false;
                }

                foreach ($rates_array['Time Series FX (Monthly)'] as $key => $value) {
                    if (str_contains($key, "$year-$padded_month")) {
                        CurrencyRate::create([
                            'currency_id' => $this->id,
                            'year' => $year,
                            'month' => $month,
                            'rate_to_usd' => $value["4. close"]
                        ])->save();
                        return $value["4. close"];
                    }
                }
                // Котировки может не быть в первые дни месяца (например выходные или запрос рано ночью 1-го)
                // Тогда тупо берем последнюю
                return reset($rates_array)["4. close"];
            } else {
                return false;
            }
        }


    }
}
