<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Transaction extends EloquentModel
{
    /**
     * Cast an attribute to a native PHP type.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function dateToCompare()
    {
        if ( class_basename($this) == 'Cashout') {
                return isset($this->left_balance_date) ? $this->left_balance_date : $this->ordered_date;
        } else {
            return isset($this->reached_balance_date) ? $this->reached_balance_date : $this->ordered_date;
        }
    }
}
