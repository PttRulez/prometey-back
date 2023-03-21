<?php

namespace App\Models;

use App\Filters\AccountFilter;
use App\Traits\RecordsActivity;
use Carbon\Carbon;

class Account extends MyModel
{
    use RecordsActivity;

    protected $guarded = [];

    public $timestamps = false;

    const STATUS_ACTIVE = 1;
    const STATUS_STOPPED = 2;
    const STATUS_BANNED = 3;
    const STATUS_QUIT = 4;

    protected $casts = [
        'disciplines' => 'array',
        'limits' => 'array',
        'limits_group' => 'array',
        'creation_date' => 'date'
    ];

    protected $hidden = ['status_name'];

    public function scopeFilter($query, AccountFilter $filters)
    {
        return $filters->apply($query);
    }

    public function scopeStoppedOrActive($query)
    {
        return $query->where('status_id', '<=', Account::STATUS_STOPPED);
    }

    public function brain()
    {
        return $this->belongsTo(Brain::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function network()
    {
        return $this->hasOneThrough(Network::class, Room::class);
    }

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function proxy()
    {
        return $this->belongsTo(Proxy::class)->withTrashed();
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function nonGameProfits()
    {
        return $this->hasMany(NonGameProfit::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function cashouts()
    {
        return $this->hasMany(Cashout::class);
    }

    public function futureCashouts()
    {
        return $this->hasMany(FutureCashout::class);
    }

    public static function statusList()
    {
        return [
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_STOPPED => 'Приостановлен',
            self::STATUS_BANNED => 'БАН',
            self::STATUS_QUIT => 'Выведен из игры'
        ];
    }

    public static function shiftList()
    {
        return [
            1 => 'утро',
            2 => 'день',
            3 => 'вечер'
        ];
    }

    public function isActive()
    {
        if ($this->room->isActive()) {
            return $this->status_id == self::STATUS_ACTIVE;
        }
        return false;
    }

    public function isActiveOrStopped()
    {
        if ($this->room->isActive()) {
            return in_array($this->status_id, [self::STATUS_ACTIVE, self::STATUS_STOPPED]);
        }
        return false;
    }

    public function isStopped()
    {
        return $this->status_id == self::STATUS_STOPPED;
    }

    public function getStatusNameAttribute()
    {
        return array_key_exists($this->status_id, static::statusList()) ? static::statusList()[$this->status_id] : 'Нет статуса';
    }

    public function bobReports()
    {
        return $this->hasMany(BobReport::class);
    }

    public function thisMonthReport()
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $thisMonthReport = BobReport::where([
            ['account_id', '=', $this->id],
            ['year', '=', $year],
            ['month', '=', $month],
        ])->first();

        return $thisMonthReport ? $thisMonthReport : false;
    }

    public function createThisMonthReport()
    {
        if (!$this->thisMonthReport() == false) {
            return;
        }
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $report = BobReport::create([
            'account_id' => $this->id,
            'year' => $year,
            'month' => $month,
        ]);
        $report->copyStartBankrollFromPrevious();
    }

    public function bobId()
    {
        return $this->belongsTo(BobId::class)->withTrashed();
    }

    public function getShiftNameAttribute()
    {
        $shiftList = static::shiftList();

        return array_key_exists($this->shift_id, $shiftList) ? $shiftList[$this->shift_id] : 'Смена не задана';
    }

    public function limitsString()
    {
        if ($this->limits)
            return implode(' ', $this->limits);
        else
            return '';
    }

    public function getComment()
    {
        if ($this->room->isStopped()) {
            if ($this->comment)
                return $this->comment;
            else
                return 'Весь рум ' . $this->room->name . ' приостановлен';
        } else {
            if ($this->comment)
                return $this->comment;
            else
                return 'Комментарий не написан';
        }


    }

    public function timetableClass()
    {
        $class = "text-center";

        if ($this->isStopped()) {
            $class .= ' bg-warning text-dark';
        } elseif ($this->room->isStopped()) {
            $class .= ' bg-secondary text-white';
        } elseif ($this->isActive()) {
            $class .= ' bg-info text-white';
        }

        return $class;
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function historyProxies()
    {
        return $this->belongsToMany(Proxy::class, 'proxy_histories')
            ->withTimestamps();
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array = array_merge($array, [
            'shift' => $this->shiftName,
            'status' => $this->statusName
        ]);

        return $array;
    }

}
