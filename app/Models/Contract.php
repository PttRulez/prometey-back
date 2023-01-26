<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'network_id'];

    public function profile()
    {
        return $this->hasMany(Profile::class);
    }

    public function network()
    {
        return $this->belongsTo(Network::class);
    }
}
