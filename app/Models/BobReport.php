<?php

namespace App\Models;

use App\Filters\BobReportFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class BobReport extends Model
{
    protected $fillable = [
        'bankroll_start',
        'bankroll_finish',
        'hands_played',
        'win',
        'total',
        'account_id',
        'year',
        'month',
        'currency_id',
    ];

    public $timestamps = true;

    protected $attributes = array(
        'bankroll_start' => 0,
        'bankroll_finish' => 0,
        'hands_played' => 0,
        'win' => 0,
        'total' => 0
    );

    protected $casts = [
        'hands' => 'integer',
        'total' => 'integer',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function bobId()
    {
        return $this->belongsTo(BobId::class);
    }

    public function canceledCashouts()
    {
        return Cashout::
        where('account_id', $this->account_id)
            ->where('type_id', Cashout::TYPE_FROM_LOBBY)
            ->whereMonth('returned_balance_date', $this->month)
            ->whereYear('returned_balance_date', $this->year)
            ->get();
    }

    public function cashouts()
    {
        return Cashout::
        where('account_id', $this->account_id)
            ->whereMonth('left_balance_date', $this->month)
            ->whereYear('left_balance_date', $this->year)
            ->get();
    }

    public function cashoutsForReport()
    {
        return Cashout::
        where([
            'account_id' => $this->account_id,
            'type_id' => Cashout::TYPE_FROM_LOBBY
        ])
            ->whereMonth('left_balance_date', $this->month)
            ->whereYear('left_balance_date', $this->year)
            ->get()->map(function ($item) {
                $item->dates = $item->dates();
                return $item;
            });
    }

    public function copyStartBankrollFromPrevious()
    {
        // Выбираем месяц и год предыдущего боб-репорта для автозаполения банкролла старта
        if ($this->month == 1) {
            $month = 12;
            $year = $this->year - 1;
        } else {
            $month = $this->month - 1;
            $year = $this->year;
        }

        // Находим предыдщий репорт
        $previous = BobReport::where([
            ['account_id', '=', $this->account_id,],
            ['year', '=', $year],
            ['month', '=', $month],
            ['id', '!=', $this->id]
        ])->get();

        // Проверяем оказалось ли больше одного предыдущего репорта (дубли). Чего быть не должно
        // Если репорт только один, то записываем в банкролл старт банкролл конца предыдущего репорта
        if ($previous->count() > 0) {
            if ($previous->count() > 1)
                dd('Каким-то макаром поулчилось, что есть дубли для ' . $this->account->nickname . ' в ' . $this->monthName() . ' ' . $year .
                    ' обратитесь к уважаемому программисту');
            $this->bankroll_start = $previous->first()->bankroll_finish;
        }
        return $this;
    }

    public function countHandsPlayed()
    {
        $this->hands_played = $this->account->sessions->filter(function ($item, $key) {
            return ($item->created_at->month == $this->month && $item->created_at->year == $this->year);
        })->sum('hands');

        return $this;
    }

    public function countTotalAndWin()
    {
        Log::info('countTotalAndWin');
        $win = 0;
        $total = 0;

        if (is_numeric($rate = $this->getCurrencyRate())) {
            $base = $this->bankroll_finish
                - $this->bankroll_start
                + $this->cashoutsForReport()->sum('amount')
                - $this->deposits()->sum('amount');

            $this->win = (int)(($base - $this->nonGameProfits()->sum('amount')) / $rate);
            $this->total = (int)($base / $rate);
        } else {
            $this->win = $rate;
            $this->total = $rate;
        }

        return $this;
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function deposits()
    {
        return Deposit::
        where('account_id', $this->account_id)
            ->whereMonth('reached_balance_date', $this->month)
            ->whereYear('reached_balance_date', $this->year)
            ->get();
    }

    public function getCurrencyRate()
    {
        $currency = $this->currency;

        if ($currency->id == 1)
            return 1;

        if ($currency) {
            $rate = $currency->getRate($this->year, $this->month);
            if ($rate)
                return $rate;
            else {
                return 1111111;
            }
        }
    }

    public function getMonthYearString()
    {
        return getMonthList()[$this->month] . ' ' . $this->year;
    }

    public function nonGameProfits()
    {
        return NonGameProfit::
        where('account_id', $this->account_id)
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->get();
    }

    public function monthName()
    {
        return getMonthList()[$this->month];
    }

    public function room()
    {
        return $this->hasOneThrough(Room::class, Account::class);
    }

    public function updateReport()
    {
        $this->countHandsPlayed()->countTotalAndWin()->save();
    }

    //Scopes
    public function scopeFilter($query, BobReportFilter $filters)
    {
        return $filters->apply($query);
    }
}
