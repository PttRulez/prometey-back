<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingCashout extends Model
{
    protected $guarded = [];

    protected $dates = ['ordered_date'];
}
