<?php

namespace App\Http\Controllers\Api;

use App\Models\Currency;
use App\Http\Controllers\Controller;
use App\Models\Computer;
use App\Models\MobileClub;
use App\Models\MobileLeague;
use App\Models\Proxy;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\MobileAccount;
use App\Models\Account;
use App\Models\Affiliate;

class HelpersController extends Controller
{
    public static function getYearList()
    {
        return getYearList();
    }

    public static function getMonthList()
    {
        return getMonthList();
    }

    public function getNetworkList(Request $request)
    {
        if ($request['page'] == "timetable") {
            return Account::with('room')->where('status_id', '<=', Account::STATUS_STOPPED)->get()
                ->filter(function ($item, $key) {
                    return !$item->room->isNotPlayedNow();
                })->pluck('room.network')->unique('name')->pluck('name', 'id');
        }

        return getNetworkList();
    }

    public function getAffiliateList(Request $request)
    {
        if ($request['page'] == "timetable") {
            return Account::with('room')->where('status_id', '<=', Account::STATUS_STOPPED)->get()
                ->filter(function ($item, $key) {
                    return !$item->room->isNotPlayedNow();
                })->pluck('affiliate')->unique('name')->mapWithKeys(function ($item) {
                    if ($item == null)
                        return ["" => 'Без аффа'];
                    return [$item['id'] => $item['name']];
                });
        }

        return getAffiliateList();
    }

    public function getAccountList()
    {
        return Account::orderBy('nickname')->pluck('nickname', 'id');
    }

    public function getCurrencyList()
    {
        return Currency::pluck('name', 'id');
    }

    public function getMobileRoomList()
    {
        return Room::where('mobile', true)->get()->pluck('name', 'id');
    }

    public function getComputerList()
    {
        return Computer::all()->toArray();
    }

}
