<?php

namespace App\Http\Controllers\Api\BobReport;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BobReport;
use Carbon\Carbon;

class CreateMonthController extends Controller
{
    public function __invoke()
    {
        $now = Carbon::now();
        $month = request('month') ?? $now->month;
        $year = request('year') ?? $now->year;

        $accounts = Account::with('room')
            ->with(['sessions' => function ($q) use ($month, $year) {
                $q->whereMonth('created_at', $month);
                $q->whereYear('created_at', $year);
            }])
            ->where('status_id', '<=', Account::STATUS_STOPPED)
            ->whereDoesntHave('bobReports', function ($q) use ($month, $year) {
                $q->where([
                    'month' => $month,
                    'year' => $year
                ]);
            })
            ->get()
            ->filter(function ($item, $key) {
                if ($item->room->isNotPlayedNow() || $item->room->isStopped())
                    return false;
                return true;
            });

        $addedNicknames = '';
        $reports = [];
        foreach ($accounts as $account) {
            $report = new BobReport([
                'account_id' => $account->id,
                'year' => $year,
                'month' => $month,
                'currency_id' => $account->currency_id,
                'hands_played' => $account->sessions->sum('hands')
            ]);
            $reports[] = $report->copyStartBankrollFromPrevious()->toArray();
            $addedNicknames .= $account->nickname . ' - ';
        }

        BobReport::insert($reports);

        if ($addedNicknames == '') {
            $msg = "Нечего добавлять, всё создано";
        } else {
            $msg = 'Месяц ' . getMonthList()[$month] . $year .
                ' заполнен записями по всем активным и приостановленным на сегодня аккаунтам: '
                . $addedNicknames;
        }
        return [
            'message' => $msg
        ];
    }
}
