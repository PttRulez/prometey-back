<?php

namespace App\Models;

use App\Models\ProxyProvider;
use App\Models\Account;
use App\Models\MobileAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\ProxyFilter;

class Proxy extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public $timestamps = false;

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
