<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionLog extends Model
{
    protected $fillable = [
    	'session_id',
    	'bob_id',
    	'nickname',
    	'hands',
    	'finish',
    	'open_tables',
    	'errors',
    	'imback',
    	'hands_in_session',
    	'hands_in_request',
    	'hands_summed'
    ];

    public function session()
    {
        return $this->belongsTo(Session::class, 'id');
    }
}
