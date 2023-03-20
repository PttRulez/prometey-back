<?php

namespace App\Models;

use App\Filters\ProxyFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proxy extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'active' => 'boolean'
    ];


    public function proxyProvider()
    {
        return $this->belongsTo(ProxyProvider::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function mobileAccounts()
    {
        return $this->hasMany(MobileAccount::class);
    }

    public function historyAccounts()
    {
        return $this->belongsToMany(Account::class, 'proxy_histories')
            ->withTimestamps();
    }

    public function scopeFilter($query, ProxyFilter $filters)
    {
        return $filters->apply($query);
    }
}
