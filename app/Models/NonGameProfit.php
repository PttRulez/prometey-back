<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NonGameProfit extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $dates = ['date'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function type()
    {
        return $this->belongsTo(NonGameProfitType::class, 'type_id');
    }

    public function typeName()
    {
        return $this->type->name;
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array = array_merge($array, [
            'date' => $this->date->locale('ru')->isoFormat('D MMMM Y'),
            'typeName' => $this->typeName(),
            'editRoute' => route('non-game-profits.edit', ['id' => $this->id]),
        ]);

        return $array;
    }
}
