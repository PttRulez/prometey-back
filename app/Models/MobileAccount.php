<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\MobileClub;
use App\Models\Computer;
use App\Models\Proxy;

class MobileAccount extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $dates = [
        'created_date',
        'banned_date',
    ];

    protected $casts = [
        'created_date' => 'datetime:Y-m-d',
        'banned_date' => 'datetime:Y-m-d',
    ];

    public function mobileClub()
    {
        return $this->belongsTo(MobileClub::class);
    }

    public function computer()
    {
        return $this->belongsTo(Computer::class);
    }
    public function proxy()
    {
        return $this->belongsTo(Proxy::class);
    }

    public function setBannedDate($banned_date)
    {
        return $this->attributes['banned_date'] = ($banned_date != '') ? $banned_date : NULL;
    }

    public function toArray()
    {
        $array = parent::toArray();

        $agentId = $this->mobileClub->agent_id ? ' - ' . $this->mobileClub->agent_id : '';
        $clubNamePlusIds = $this->mobileClub->name . ' - ' . $this->mobileClub->club_id . $agentId;

        $array = array_merge($array, [
            'clubNamePlusIds' => $clubNamePlusIds,
            'computerName' => $this->computer ? $this->computer->name : 'Нет компьютера',
            'roomName' => $this->mobileClub->mobileLeague->room->name
        ]);

        return $array;
    }
}
