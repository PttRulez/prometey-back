<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_STOPPED = 2;
    const STATUS_NOT_PLAYED_NOW = 3;

    protected $guarded = [];

    public $timestamps = false;

    public function network()
    {
        return $this->belongsTo(Network::class);
    }

    public static function statusList()
    {
        return [
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_STOPPED  => 'Приостановлен',
            self::STATUS_NOT_PLAYED_NOW  => 'Не играется сейчас',
        ];
    }

    public function isActive()
    {
        return $this->status_id == self::STATUS_ACTIVE;
    }

    public function isStopped()
    {
        return $this->status_id == self::STATUS_STOPPED;
    }

    public function isNotPlayedNow()
    {
        return $this->status_id == self::STATUS_NOT_PLAYED_NOW;
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function scopeIsPlayedNow($query)
    {
        return $query->where('status_id', '<=', 2);
    }
}
