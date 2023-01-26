<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const STATUS_ADMIN = 1;
    const STATUS_MANAGER = 2;
    const STATUS_ACCOUNTANT = 3;

    public static function roleList()
    {
        return [
            self::STATUS_ADMIN => 'admin',
            self::STATUS_MANAGER => 'manager',
            self::STATUS_ACCOUNTANT => 'accountant'
        ];
    }

}
