<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProxyHistory extends Model
{
    public function proxy()
    {
        return $this->belongsTo(Proxy::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
