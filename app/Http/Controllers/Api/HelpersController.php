<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Brain;
use App\Models\Computer;
use App\Models\Currency;
use App\Models\Network;
use App\Models\Room;
use Illuminate\Http\Request;

class HelpersController extends Controller
{
    public function getAccountList()
    {
        return Account::orderBy('nickname')->pluck('nickname', 'id');
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

    public function getBrainsList()
    {
        return Brain::all()->pluck('name', 'id')->toArray();
    }

    public function getComputerList()
    {
        return Computer::all()->toArray();
    }

    public function getCurrencyList()
    {
        return Currency::pluck('name', 'id');
    }

    public function getMobileRoomList()
    {
        return Room::where('mobile', true)->get()->pluck('name', 'id');
    }

    public static function getMonthList()
    {
        return getMonthList();
    }

    public function getNetworkList(Request $request)
    {
        return Network::all()->pluck('name', 'id')->toArray();
    }

    public function getTimeTableSelectLists(Request $request)
    {
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
            'affiliateList' => $affiliateList,
            'networkList' => $networkList
        ];
    }

    public static function getYearList()
    {
        return getYearList();
    }

}
