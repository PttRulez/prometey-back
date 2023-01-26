<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Session extends Model
{
    protected $fillable = [
        'bob_id',
        'account_id',
        'nickname',
        'finish',
        'hands',
        'imback',
        'errors',
        'open_tables'
    ];

    protected $appends = array('day');

    public function getDayAttribute()
    {
        return $this->created_at->locale('ru')->isoFormat('D MMM');
    }

    public static function boot()
    {
        parent::boot();

        self::updated(function ($model) {
            $bobReport = BobReport::where([
                'account_id' => $model->account_id,
                'year' => $model->created_at->year,
                'month' => $model->created_at->month
            ])->first();

            if ($bobReport)
                $bobReport->touch();
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function logs()
    {
        return $this->hasMany(SessionLog::class, 'session_id');
    }

    public function latestLog()
    {
        return $this->hasOne(SessionLog::class)->latest();
    }

    public function hasProblems()
    {
        return $this->longNoInfo() || $this->zeroTables();
    }

    public function longNoInfo()
    {
        $now = Carbon::now();
        return $this->updated_at->diffInMinutes($now) > 19;
    }

    public function zeroTables()
    {
        $now = Carbon::now();
        return $this->open_tables == 0 && $this->created_at->diffInMinutes($now) > 11;
    }

    // ==================================================================== //

    public static function getSession($request)
    {
        return Session::where([
            'finish' => 0,
            'bob_id' => (int)$request->get('account'),
        ])->first();
    }

    public static function makeNewSession($request)
    {
        $requestData = $request->all();
        $bobId = (int)$requestData['account'];
        $account = BobId::where('bob_id', $bobId)->first()->activeAccount();

        $sessionData = array_merge($requestData, [
            'account_id' => $account->id,
            'nickname' => $account->nickname,
            'bob_id' => $bobId,
            'hands_in_session' => $requestData['hands'],
            'hands_in_request' => $requestData['hands'],
            'hands_summed' => $requestData['hands']
        ]);

        $new_session = Session::create($sessionData);
        $new_session->logs()->create($sessionData);

        return $new_session;
    }


    public function updateSession($request)
    {
        $now = Carbon::now();

        // Если сессию апдейтили более 3-х часов назад ,
        // то закрываем сессию и удаляем логи. Начинаем новую
        if ($now->diffInHours($this->updated_at) >= 3) {
            $this->update(['finish' => 1, 'updated_at' => false]);

            self::makeNewSession($request);
            return;
        }

        $requestData = $request->all();
        $bobId = (int)$requestData['account'];
        $account = BobId::where('bob_id', $bobId)->first()->activeAccount();

        $sessionData = array_merge($requestData, [
            'account_id' => $account->id,
            'bob_id' => $bobId,
            'nickname' => $account->nickname,
            'hands' => $this->hands + $requestData['hands'],
            'updated_at' => $now->format('Y-m-d H:i:s')
        ]);

        $sessionLogData = array_merge($requestData, [
            'nickname' => $account->nickname,
            'bob_id' => $bobId,
            'hands_in_session' => $this->hands,
            'hands_in_request' => $requestData['hands'],
            'hands_summed' => $this->hands + $requestData['hands']
        ]);

        $this->logs()->create($sessionLogData);
        $this->update($sessionData);
        $this->touch();
    }
}
