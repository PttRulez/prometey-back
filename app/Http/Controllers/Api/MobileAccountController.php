<?php

namespace App\Http\Controllers\Api;

use App\Models\MobileAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Computer;
use App\Models\MobileClub;
use App\Models\Proxy;

class MobileAccountController extends Controller
{
    public function index()
    {
        return view('mobile-accounts.index', compact('models'));
    }

    public function show(MobileAccount $mobileAccount)
    {
        return $mobileAccount->load('mobileClub', 'proxy', 'computer');
    }

    public function store(Request $request)
    {
        $account = MobileAccount::create($this->validateRequest());

        if (request()->wantsJson()) {
            return $account;
        } else {
            return redirect('/mobile-accounts')->with('status', 'Создан новый Акк');;
        }
    }

    public function update(Request $request, MobileAccount $mobileAccount)
    {
        $mobileAccount->fill($this->validateRequest())->save();

        if (request()->wantsJson()) {
            return $mobileAccount;
        } else {
            return redirect()->route('mobile-accounts.index')->with('status', 'Акк изменён');;
        }
    }

    public function destroy(MobileAccount $mobileAccount)
    {
        $mobileAccount->delete();
    }

    public function validateRequest()
    {
        if (request()->method() =="POST") {
            $playerIdRule = 'required|unique:mobile_accounts';
        } else {
            $playerIdRule = 'required|unique:mobile_accounts,player_id,' . request('id');
        }

        return request()->validate(
            [
                'bobid'             => 'nullable',
                'game_type'         => 'required',
                'computer_id'       => 'required',
                'player_id'         => $playerIdRule,
                'nickname'          => 'required',
                'mobile_club_id'    => 'required',
                'proxy_id'          => 'required',
                'login'             => 'required',
                'password'          => 'required',
                'roll_start'        => 'required',
                'roll_end'          => 'nullable',
                'created_date'      => 'required',
                'banned_date'       => 'nullable',
                'info'              => 'nullable'
            ],
            [

            ]);
    }

    public function getActiveMobileAccounts()
    {
        return MobileAccount::where('banned', false)->with('proxy')->with('mobileClub')
            ->with('computer')->with('mobileClub.mobileLeague')->with('mobileClub.mobileLeague.room')->get();
    }

    public function getActiveMobileClubs()
    {
        return MobileClub::where(['activity_status' => 1])->get()->toArray();
    }

    public function getBannedMobileAccounts()
    {
        $bannedAccs = MobileAccount::where('banned', true)->with('proxy')->with('mobileClub')
            ->with('mobileClub.mobileLeague')->get()->sortByDesc('banned_date');
//        dd($bannedAccs);
        return $bannedAccs;
    }

    public function ban(MobileAccount $mobileAccount)
    {
        $mobileAccount->banned = TRUE;
        $mobileAccount->banned_date = Carbon::now();
        $mobileAccount->save();
    }

    public function unban(MobileAccount $mobileAccount)
    {
        $mobileAccount->banned = FALSE;
        $mobileAccount->banned_date = NULL;
        $mobileAccount->save();
    }

    public function getProxiesForMobileClub(Request $request)
    {
        $allproxies = Proxy::withoutTrashed()->get();
        $leagueId = MobileClub::find(request('club_id'))->mobileLeague->id;

        //Находим все аккаунты в этой лиге и в $used_proxy_ids записываем id всех прокси, которые они использовали
        $accountsFromLeague = MobileAccount::whereHas('mobileClub', function ($query) use($leagueId) {
            return $query->where('mobile_league_id', '=', $leagueId);
        })->get();

        // Исключаем прокси самой модели
        if(request('mobile_account_id'))
            $accountsFromLeague = $accountsFromLeague->reject(function ($item) {
                return $item->id == request('mobile_account_id');
            });
        $used_proxy_ids = $accountsFromLeague->pluck('proxy_id')->toArray();


        $result = $allproxies->filter(function ($item, $key) use($used_proxy_ids){
            // Исключаем Украинские и Российские проксики
            if (stringInArray($item, ['Russia', 'Ukraine'])) {
                return false;
            }

            // Исключаем проксики, уже использованные в данной лиге
            return !in_array($item->id, $used_proxy_ids);
        });

        $result = $result->sortBy('name', SORT_NATURAL, false)->values();

        return $result;
    }
}
