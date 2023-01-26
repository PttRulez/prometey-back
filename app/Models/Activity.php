<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $guarded = [];

    protected $casts = [
        'changes' => 'array'
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activitiesStrings($changes)
    {
        $activityType = explode('_', $this->description)[0];

        if ($activityType == 'updated') {

            $before = $changes['before'];
            $after = $changes['after'];

            $result = [];
            foreach ($after as $key => $value) {
                $result[] = array_key_exists($key, $before) ?
                    $key . ' сменил с ' . $before[$key] . ' на ' . $value
                    : $key . ' поставил ' . $value;
            }

            return $result;
        } else {
            return [];
        }

    }
}

