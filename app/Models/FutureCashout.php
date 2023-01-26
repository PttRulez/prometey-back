<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FutureCashout extends Model
{
    protected $fillable = ['amount', 'date', 'account_id', 'user_id'];

    protected $dates = ['date'];

    public $timestamps = false;

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->user->name;
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array = array_merge($array, [
            'createdBy' => $this->createdBy(),
            'networkName' => $this->account->room->network->name,
        ]);

        return $array;
    }
}
