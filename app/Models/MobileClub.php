<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MobileLeague;

class MobileClub extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function mobileLeague()
    {
        return $this->belongsTo(MobileLeague::class);
    }

    public function getStatusAttribute()
    {
        $statuses = [
            1 => 'Активен',
            2 => 'Неактивен',
            3 => 'Не принимает новых'
        ];

        return $statuses[$this->activity_status];
    }

    public function toArray()
    {
        $array = parent::toArray();
        $agentId = $this->agent_id ? ' - ' . $this->agent_id : '';

        $array = array_merge($array, [
            'room_id' => $this->mobileLeague->room->id,
            'status' => $this->status
        ]);

        return $array;
    }
}
