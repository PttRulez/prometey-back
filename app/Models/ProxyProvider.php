<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProxyProvider extends Model
{
    protected $fillable = ['name', 'info'];

    public $timestamps = false;
}
