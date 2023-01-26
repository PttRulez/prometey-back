<?php

namespace App\Models;
use App\Models\Account;

use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
    protected $fillable = ['name', 'contact', 'info'];

    public $timestamps = false;

    public function rooms()
    {
        return $this->hasMany(Account::class);
    }
}
