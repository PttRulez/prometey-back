<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use Carbon\Carbon;

class Deposit extends Transaction
{
    use RecordsActivity;

    //      Сценарии
    const SCENARIO_INSTANT = 1;
    const SCENARIO_ORDER_FIRST = 2;

    protected $guarded = [];

    public $timestamps = false;

    protected $dates = ['ordered_date', 'reached_balance_date'];

    protected $casts = [
        'ordered_date' => 'datetime:Y-m-d',
        'reached_balance_date' => 'datetime:Y-m-d',
    ];

    public static function scenarioList()
    {
        return [
            self::SCENARIO_INSTANT => 'Деньги сразу падают на баланс',
            self::SCENARIO_ORDER_FIRST => 'Деп сперва заказывается у аффа, позже падает на баланс',
        ];
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function pending()
    {
        return $this->reached_balance_date == null;
    }

    public function trClass()
    {
        if ($this->pending())
            return 'bg-warning';
        else
            return 'bg-primary';
    }

    public function scenario()
    {
        return $this->account->room->deposit_scenario;
    }

    public function instant()
    {
        return $this->scenario() == self::SCENARIO_INSTANT;
    }

    public function orderFromAff()
    {
        return $this->scenario() == self::SCENARIO_ORDER_FIRST;
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array = array_merge($array, [
            'orderFromAff' => $this->orderFromAff(),
            'trClass' => $this->trClass()
        ]);

        return $array;
    }

    public static function boot()
    {
        parent::boot();

        static::saved(function (Deposit $model) {
            if ($model->reached_balance_date) {
                $report = BobReport::where([
                    'account_id' => $model->account_id,
                    'year' => $model->reached_balance_date->year,
                    'month' => $model->reached_balance_date->month,
                ])->first();


                if ($report) {
                    $report->updated_at = Carbon::now();
                    $report->save();
                }
            }
        });
    }
}
