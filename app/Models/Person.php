<?php

namespace App\Models;
use App\Models\Account;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = ['name', 'email', 'email_password', 'secret_answer', 'phone', 'skype',
        'btc_wallet', 'address', 'zip', 'birthdate', 'info'];

    protected $dates = ['birthdate'];

    public $timestamps = false;

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function shortName()
    {
        list($firstWord) = explode(' ', $this->name);
        return $firstWord;
    }

    public function toArray()
    {
        $arr =  parent::toArray();

        return array_merge($arr, [
            'shortName' => $this->shortName()
        ]);
    }
}
