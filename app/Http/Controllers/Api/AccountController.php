<?php

namespace App\Http\Controllers\Api;

use App\Filters\AccountFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Models\Account;
use App\Models\Affiliate;
use App\Models\BobId;
use App\Models\BobReport;
use App\Models\Brain;
use App\Models\Cashout;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Network;
use App\Models\Person;
use App\Models\Profile;
use App\Models\Room;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * @param Request $request
     * @return Account[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function index(Request $request, AccountFilter $filters)
    {
//        $models = $this->accountRepository->getIndex();
        $models = Account::with([
            'brain',
            'room:id,network_id',
            'room.network:id,name',
            'affiliate:id,name'
        ])
            ->orderBy('nickname')
            ->filter($filters)
            ->get();

        $networkList = Account::with('room.network')
            ->get()
            ->pluck('room.network')
            ->unique('name')
            ->pluck('name', 'id');

        $affiliateList = $models->pluck('affiliate')
            ->unique()
            ->map(function ($item) {
                if ($item == null) {
                    return collect([
                        'id' => 'no_aff',
                        'name' => 'Без аффа'
                    ]);
                } else {
                    return $item;
                }
            })->pluck('name', 'id');

//        $models = $models->map(function ($item, $k) {
//            $item->shift = $item->shiftName;
//            $item->status = $item->statusName;
//            if ($item->limits_group)
//                $item->limits_group = trim(implode(' ', $item->limits_group));
//            else
//                $item->limits_group = 'Лимитная группа не задана';
//            return $item;
//        })
//            ->groupBy('room.network.name')
//            ->transform(function ($item, $k) {
//                return $item->groupBy('disciplines')->transform(function ($item, $k) {
//                    return $item->groupBy('limits_group')->transform(function ($item, $k) {
//                        return $item->groupBy('shift_id');
//                    });
//                });
//
//            });


        return compact('models', 'affiliateList', 'networkList');
    }

    public function show(Account $account)
    {
        if ($account->person()->exists()) {
            $person = Person::with('accounts.room.network')->where('id', $account->person_id)->first();
        }
        $cashouts = Cashout::with('account.room.network')
            ->where('account_id', $account->id)
            ->get()
            ->map(function ($item) {
                $item->type = 'cashout';
                $item->dateToCompare = $item->dateToCompare();
                return $item;
            });

        $deposits = Deposit::with('account.room.network')
            ->where('account_id', $account->id)
            ->get()
            ->map(function ($item) {
                $item->type = 'deposit';
                $item->dateToCompare = $item->dateToCompare();
                return $item;
            });

        $transactions = $cashouts->merge($deposits);

        $report = BobReport::where([
            'account_id' => $account->id,
            'year' => now()->year,
            'month' => now()->month,
        ])->first();

        $reportId = $report ? $report->id : 0;

        $arr = [];
        foreach ($account->activity as $activity) {
            array_push($arr, [
                'author' => $activity->user->name,
                'created_at' => $activity->created_at,
                'changes' => $activity->activitiesStrings($activity->changes)
            ]);
        }
        $account->changesDone = $arr;
        $account = $account->load([
            'room.network',
            'currency',
            'activity.user',
            'bobId.profile',
            'affiliate',
            'proxy',
            'createdBy',
        ]);

        return [
            'account' => $account,
            'person' => $person ?? null,
            'transactions' => $transactions,
            'reportId' => $reportId
        ];
    }

    public function create()
    {
        return ($this->getAccountFormData());
    }

    public function store(AccountRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->user()->id;
        $acc = Account::create($validated);
        return $acc->id;
    }

    public function edit(Account $account)
    {
        return $this->getAccountFormData($account);
    }


    public function update(AccountRequest $request, Account $account)
    {
        $validated = $request->validated();
        $account->fill($validated)->save();
        return $validated;
    }

    public function destroy(Account $account)
    {
        $account->delete();
    }

    public function created()
    {
        $accounts = Account::where('creation_date', '>=', '2019-12-30')
            ->with([
                'createdBy:id,name',
                'room.network:id,name',
                'sessions'
            ])
            ->orderBy('creation_date', 'desc')
            ->take(30)
            ->get()
            ->map(function ($item, $key) {
                $item['hands'] = $item->sessions->sum('hands');
                return $item;
            });

        return $accounts;
    }

    private function getAccountFormData(Account $account = null)
    {
        // $bobIds - Список боб айдишников, у которых нет привязанных акков с первыми двумя статусами
        if ($account) {
            $bobIds = BobId::with('network')->whereDoesntHave('accounts', function ($query) {
                $query->where('status_id', '<=', Account::STATUS_STOPPED);
            })->get();

            $accountBobId = BobId::with('network')->withTrashed()->where('id', $account->bob_id_id)->first();

            if ($accountBobId) {
                $bobIds->push($accountBobId);
            }
        } else {
            $bobIds = BobId::with('network')->whereDoesntHave('accounts', function ($query) {
                $query->where('status_id', '<=', Account::STATUS_STOPPED);
            })->get();
        }

        // Функция возвращает 1) данные по аккаунту, чтобы заполнить форму (edit) или пустую модель
        // аккаунта (create)
        // 2) Список румов, Список бобайди,  Спиок лимитов, Спиок смен, Список Аффилейтов,
        // Список Физиков,
        // Список проксиков, Список статусов для выпадающих списков
        return [
            'account' => $account ? $account : null,
            'brainsList' => Brain::pluck('name', 'id'),
            'roomList' => Room::with('network')->orderBy('name')->get(),
            'personList' => Person::select('id', 'name')->orderBy('name')->get(),
            'existingLimits' => BobId::$existingLimits,
            'shiftList' => Account::shiftList(),
            'currencyList' => Currency::pluck('name', 'id'),
            'bobIdList' => $bobIds,
            'affiliateList' => Affiliate::select('id', 'name')->orderBy('name')->get(),
//            'proxyList' => Proxy::orderBy('name')->get()->sortBy('name', SORT_NATURAL, false)->pluck('name', 'id'),
            'statusList' => Account::statusList(),
        ];
    }

    public function prepareFormData($id)
    {
        return [
            'account' => Account::find($id),
            'brainsList' => Brain::pluck('name', 'id'),
            'profileList' => Profile::with(['contract.network', 'accounts'])->get(),
            'roomList' => Room::with('network')->orderBy('name')->get(),
            'personList' => Person::select('id', 'name')->orderBy('name')->get(),
            'currencyList' => Currency::pluck('name', 'id'),
            'affiliateList' => Affiliate::select('id', 'name')->orderBy('name')->get(),
        ];
    }

    public function forHm(Request $request, AccountFilter $filters)
    {

        $accounts = Account::select('id', 'nickname', 'disciplines', 'limits', 'room_id')
            ->with('room:id,name')
            ->filter($filters)
            ->get();

        $networkList = Network::all('id', 'name');

        return compact('accounts', 'networkList');
    }

    public function timetable(Request $request, AccountFilter $filters)
    {
        $models = Account::whereHas('room', function ($q) {
            $q->isPlayedNow();
        })
            ->with('room.network', 'affiliate')
            ->stoppedOrActive()
            ->filter($filters)
            ->orderBy('shift_id')->get()
            ->map(function ($item, $k) {
                if ($item->limits_group)
                    $item->limits_group = trim(implode(' ', $item->limits_group));
                else
                    $item->limits_group = 'Лимитная группа не задана';
                $item->timetableClass = $item->timetableClass();
                return $item;
            })->groupBy('room.network.name')
            ->transform(function ($item, $k) {
                return $item->groupBy('disciplines')->transform(function ($item, $k) {
                    return $item->groupBy('limits_group')->sortBy(function ($item, $k) {
                        return (int)explode(' ', $k)[0];
                    })
                        ->transform(function ($item, $k) {
                            return $item->groupBy('shift_id');
                        });
                });

            });

//        $models = $models->mapWithKeys(function ($value, $key) {
//            $networkId = Network::where('name', $key)->first()->id;
//            $freeBobIdsCountForNetwork = BobId::where('network_id', $networkId)->whereDoesntHave('accounts', function ($query) {
//                $query->where('status_id', '<=', Account::STATUS_STOPPED);
//            })->count();
//            return [$key . ' - ' . $freeBobIdsCountForNetwork => $value];
//        });


        $accsForLists = Account::whereHas('room', function ($q) {
            $q->isPlayedNow();
        })->stoppedOrActive()->with('room.network', 'affiliate')->get();

        $networkList = $accsForLists
            ->pluck('room.network')
            ->unique('name')
            ->pluck('name', 'id');

        $affiliateList = $accsForLists
            ->pluck('affiliate')
            ->filter(function ($item) {
                return $item != null;
            })
            ->unique('name')
            ->mapWithKeys(function ($item) {
                return [$item['id'] => $item['name']];
            });

        return [
            'models' => $models,
            'networkList' => $networkList,
            'affiliateList' => $affiliateList
        ];
    }

    public function profitByMonths(Request $request)
    {
        $account = Account::find($request->get('id'));

        $bobReports = $account->bobReports->map(function ($item, $key) {
            $item->monthName = $item->getMonthYearString();
            return $item;
        });

        $monthList = $bobReports->pluck('monthName')->toArray();
        $profitsArray = $bobReports->pluck('total')->toArray();

        return [
            'labels' => $monthList,
            'datasets' => array([
                'label' => 'Профиты аккаунта ' . $account->nickname,
                'backgroundColor' => '#F26202',
                'borderColor' => '#F26202',
                'data' => $profitsArray
            ])
        ];
    }

    public function handsByMonths(Request $request)
    {
        $account = Account::find($request->get('id'));

        $bobReports = $account->bobReports->map(function ($item, $key) {
            $item->monthName = $item->getMonthYearString();
            return $item;
        });

        $monthList = $bobReports->pluck('monthName')->toArray();
        $handsArray = $bobReports->pluck('hands_played')->toArray();

        return [
            'labels' => $monthList,
            'datasets' => array([
                'label' => 'Сыграно рук - ' . $account->nickname,
                'backgroundColor' => '#F26202',
                'borderColor' => 'blue',
                'data' => $handsArray
            ])
        ];
    }


}
