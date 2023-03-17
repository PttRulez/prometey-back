<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use Carbon\Carbon;

class Cashout extends Transaction
{
    use RecordsActivity;

    //      Статус
    const STATUS_PENDING = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_CANCELED = 3;

    //      Тип
    const TYPE_FROM_LOBBY = 1;
    const TYPE_FROM_MAIN = 2;
    const TYPE_TO_BE_DONE = 3;

    //      Сценарии
    const SCENARIO_TO_MAIN = 1;
    const SCENARIO_ORDER_FROM_AFF = 2;
    const SCENARIO_COMES_BACK_IF_CANCELED = 3;
    const SCENARIO_INSTANT = 4;

    protected $guarded = [];

    protected $attributes = [
        'type_id' => self::TYPE_FROM_LOBBY,
    ];

    public $timestamps = false;

    protected $dates = [
        'ordered_date',
        'left_balance_date',
        'returned_balance_date',
    ];

    public static function statusList()
    {
        return [
            self::STATUS_PENDING => 'Ждем',
            self::STATUS_SUCCESS => 'Завершен',
            self::STATUS_CANCELED => 'Отмена',
        ];
    }

    public function pending()
    {
        return $this->status_id == self::STATUS_PENDING;
    }

    public function successful()
    {
        return $this->status_id == self::STATUS_SUCCESS;
    }

    public function canceled()
    {
        return $this->status_id == self::STATUS_CANCELED;
    }

    public function isFuture()
    {
        return $this->type_id == self::TYPE_TO_BE_DONE;
    }

    public static function cashoutScenarios()
    {
        return [
            self::SCENARIO_TO_MAIN => 'Деньги уходят сразу из лобби на мейн аккаунт',
            self::SCENARIO_ORDER_FROM_AFF => 'Сперва заказ у аффилейта, потом уходят из лобби (как, например в Спартане)',
            self::SCENARIO_COMES_BACK_IF_CANCELED => 'Деньги уходят сразу из лобби, но при отмене падают обратно на счет в лобби (Grey SNow)',
            self::SCENARIO_INSTANT => 'Деньги сразу уходят с баланса и кэш не отменяется',
        ];
    }

    public function scenario()
    {
        return $this->account->room->cashout_scenario;
    }

    public function goesToMain()
    {
        return $this->scenario() == self::SCENARIO_TO_MAIN;
    }

    public function goesFromMain()
    {
        return $this->type_id == self::TYPE_FROM_MAIN;
    }

    public function orderFromAff()
    {
        return $this->scenario() == self::SCENARIO_ORDER_FROM_AFF;
    }

    public function comesBackIfCanceled()
    {
        return $this->scenario() == self::SCENARIO_COMES_BACK_IF_CANCELED;
    }

    public function instant()
    {
        return $this->scenario() == self::SCENARIO_INSTANT;
    }

    public function getStatusAttribute()
    {
        if ($this->isFuture()) {
        } else if ($this->status_id) {
            return static::statusList()[$this->status_id];
        } else {
            return '';
        }

    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function whenOrdered()
    {
        if ($this->instant()) {
            return $this->left_balance_date;
        } else {
            return $this->ordered_date ?? null;
        }
    }

    public function trClass()
    {
        if ($this->isFuture())
            return 'bg-danger';
        if ($this->pending())
            return 'bg-warning';
        if ($this->successful())
            return 'bg-success';
        if ($this->canceled())
            return 'bg-danger';
    }

    public function dates()
    {
        $dates = [];

        if ($this->comesBackIfCanceled()) {
            $dates[0] = $this->left_balance_date->locale('ru')->isoFormat('D MMMM Y');
            $dates[1] = $this->returned_balance_date ?
                $this->returned_balance_date->locale('ru')->isoFormat('D MMMM Y') : '';
        }

        if ($this->instant() || $this->goesToMain()) {
            $dates[0] = '';
            $dates[1] = $this->left_balance_date ? $this->left_balance_date->locale('ru')->isoFormat('D MMMM Y') : '';
        }

        if ($this->goesFromMain() || $this->orderFromAff()) {
            $dates[0] = $this->ordered_date->locale('ru')->isoFormat('D MMMM Y');
            $dates[1] = $this->left_balance_date ?
                $this->left_balance_date->locale('ru')->isoFormat('D MMMM Y') : '';
        }

        return $dates;
    }

    public function toArray()
    {
        $array = parent::toArray();

        $array = array_merge($array, [
            'status' => $this->status,
            'trClass' => $this->trClass(),
            'createdBy' => $this->createdBy(),
            'goesFromMain' => $this->goesFromMain(),
            'instant' => $this->instant(),
            'goesToMain' => $this->goesToMain(),
            'whenOrdered' => $this->whenOrdered(),
            'orderFromAff' => $this->orderFromAff(),
            'pending' => $this->pending(),
            'comesBackIfCanceled' => $this->comesBackIfCanceled()
        ]);

        return $array;
    }

    public static function boot()
    {
        parent::boot();

        static::created(function (Cashout $model) {
            $model->account->futureCashouts()->delete();
        });

        static::saved(function (Cashout $model) {
            if ($model->left_balance_date) {
                $report = BobReport::where([
                    'account_id' => $model->account_id,
                    'year' => $model->left_balance_date->year,
                    'month' => $model->left_balance_date->month,
                ])->first();

                if ($report) {
                    $report->updated_at = Carbon::now();
                    $report->save();
                }
            }
        });
    }
}
