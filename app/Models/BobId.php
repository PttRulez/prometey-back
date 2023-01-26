<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Filters\BobIdFilter;
use Illuminate\Database\Eloquent\SoftDeletes;

class BobId extends Model
{
    use SoftDeletes;

    protected $casts = [
        'disciplines' => 'array',
        'limits' => 'array',
    ];

    protected $guarded = [];

    public $timestamps = false;

    public static $existingLimits = [ '2', '4', '10', '20', '25', '30', '40', '50', '70', '100', '150', '200', '250', '300', '400', '500', '600', '1000',
        '2000', '3000', '4000', '5000', '10000', '15000', '20000' ];

    public static $existingDisciplines = [
            'NL HU' => 'NL HU',
            'NL 6max' => 'NL 6max',
            'NL 10max' => 'NL 10max',
            'NLS 6max' => 'NLS 6max',
            'NL6+ 6max' => 'NL6+ 6max',
            'NLR' => 'NLR',
            'PLO HU' => 'PLO HU',
            'PLO 6max' => 'PLO 6max',
            'PLO 10max' => 'PLO 10max',
            'PLOR' => 'PLOR',
            'PLO5C' => 'PLO5C'
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function network()
    {
        return $this->belongsTo(Network::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function activeAccounts()
    {
        return $this->hasMany(Account::class)->where('status_id', '<', 2);
    }

    public function activeAccount()
    {
        return $this->activeAccounts->first();
    }

    public function scopeFilter($query, BobIdFilter $filters)
    {
        return $filters->apply($query);
    }
}
