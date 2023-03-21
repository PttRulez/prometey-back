<?php

namespace App\Models;

use App\Filters\ProfileFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'disciplines' => 'array',
        'limits' => 'array',
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function bobId()
    {
        return $this->hasOne(BobId::class); // система должна работать так, что у профиля только один боб ид
    }

    public function scopeFilter($query, ProfileFilter $filters)
    {
        return $filters->apply($query);
    }
}
