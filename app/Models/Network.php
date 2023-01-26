<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Room;
use App\Models\BobId;

class Network extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function BobIds()
    {
        return $this->hasMany(BobId::class);
    }

}
