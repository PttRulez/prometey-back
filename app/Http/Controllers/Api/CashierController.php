<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CashoutResource;
use App\Models\Cashout;
use App\Models\Deposit;
use App\Models\FutureCashout;
use App\Models\Network;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index(Request $request)
    {
        $cashouts = $this->filterCashier($request['params']['filters'])['cashouts'];
        $deposits = $this->filterCashier($request['params']['filters'])['deposits'];
        $futureCashouts = FutureCashout::with('account')->orderBy('date', 'desc')->get();

        return [
            'futureCashouts' => $futureCashouts,
            'cashouts' => CashoutResource::collection($cashouts),
            'deposits' => $deposits,
            'networkList' => Network::all()->sortBy('name')->pluck('name', 'id'),
            'monthList' => getMonthList(),
            'yearList' => getYearList(),
        ];
    }

    public function filterCashier($requestFilters)
    {
        $now = Carbon::now();
        $cashouts = collect([]);
        $deposits = collect([]);

        $filters = [
            'nickname' => $requestFilters['nickname'] ?? null,
            'year' => $requestFilters['year'] != null ? $requestFilters['year'] : $now->year,
            'month' => $requestFilters['month'] != null ? $requestFilters['month'] : $now->month,
            'network_id' => $requestFilters['network_id'] != null ? $requestFilters['network_id'] : null,
            'category' => $requestFilters['category'] != null ? $requestFilters['category'] : 'both',
            'wait' => $requestFilters['wait'],
        ];

        $cashoutsSql = Cashout::with('account.room.network');
        $depositsSql = Deposit::with('account.room.network');

        if ($filters['network_id']) {

            $cashoutsSql = $cashoutsSql->whereHas('account', function ($query) use ($filters) {
                $query->whereHas('room', function ($query) use ($filters) {
                    $query->where('network_id', $filters['network_id']);
                });
            });

            $depositsSql = $depositsSql->whereHas('account', function ($query) use ($filters) {
                $query->whereHas('room', function ($query) use ($filters) {
                    $query->where('network_id', $filters['network_id']);
                });
            });
        }

        if ($filters['wait']) {
            $cashoutsSql = $cashoutsSql->where('status_id', Cashout::STATUS_PENDING)->orderBy('id', 'desc');

            $depositsSql = $depositsSql->whereNull('reached_balance_date')->orderBy('id', 'desc');

        } else {
            $depositsSql = $depositsSql->where(function ($q) use ($filters) {
                $q->whereMonth('reached_balance_date', $filters['month'])
                    ->whereYear('reached_balance_date', $filters['year'])
                    ->orWhereNull('reached_balance_date');
            });

            // Отбираем завершенные кэшауты только этого месяца и все пендинг кэшауты (через orWhere большое выражение)
            $cashoutsSql = $cashoutsSql->whereMonth('left_balance_date', $filters['month'])
                ->whereYear('left_balance_date', $filters['year'])
                ->whereHas('account', function ($query) use ($filters) {
                    $query->where('nickname', 'like', '%' . $filters['nickname'] . '%');
                })
                ->orWhere(function ($q) use ($filters) {
                    if ($filters['network_id']) {
                        $q->where(['status_id' => Cashout::STATUS_PENDING])->whereHas('account', function ($query) use ($filters) {
                            $query->whereHas('room', function ($query) use ($filters) {
                                $query->where('network_id', $filters['network_id']);
                            });
                        });
                    } else {
                        $q->where(['status_id' => Cashout::STATUS_PENDING]);
                    }
                });
        }

        if ($filters['nickname']) {
            $cashoutsSql = $cashoutsSql->whereHas('account', function ($query) use ($filters) {
                $query->where('nickname', 'like', '%' . $filters['nickname'] . '%');
            });

            $depositsSql = $depositsSql->whereHas('account', function ($query) use ($filters) {
                $query->where('nickname', 'like', '%' . $filters['nickname'] . '%');
            });
        }

        if ($filters['category'] == 'cashouts') {
            $cashouts = $cashoutsSql->get();
        } else if ($filters['category'] == 'deposits') {
            $deposits = $depositsSql->get();
        } else {
            $cashouts = $cashoutsSql->get();
            $deposits = $depositsSql->get();
        }

        $cashouts = $cashouts->sort(function ($a, $b) {
            if (!$a['left_balance_date'] && !$b['left_balance_date']) {
                return $a['ordered_date'] <=> $b['ordered_date'];
            }

            if (!$a['left_balance_date']) {
                return -1;
            } else if (!$b['left_balance_date']) {
                return 1;
            }

            return -($a['left_balance_date'] <=> $b['left_balance_date']);
        });

        return [
            'cashouts' => $cashouts,
            'deposits' => $deposits,
        ];
    }
}
