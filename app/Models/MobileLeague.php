<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Room;

class MobileLeague extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
