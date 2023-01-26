<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Network;
use App\Models\Cashout;
use App\Models\Deposit;
use App\Models\Currency;
use App\Models\Proxy;
use App\Models\Account;

class RoomController extends Controller
{
    public function index()
    {
        return  Room::with('network')->orderBy('name')->get();
    }

    public function create()
    {
        return [
            'cashoutScenarios' => Cashout::cashoutScenarios(),
            'depositScenarios' => Deposit::scenarioList(),
            'statusList' => Room::statusList(),
            'currencyList' => Currency::pluck('name', 'id'),
            'networkList' => getNetworkList()
        ];
    }

    public function store(Request $request)
    {
        $room = Room::create($this->validateRoom());
        return $room->id;
    }

    public function edit(Room $room)
    {
        return [
            'room' => $room,
            'cashoutScenarios' => Cashout::cashoutScenarios(),
            'depositScenarios' => Deposit::scenarioList(),
            'statusList' => Room::statusList(),
            'currencyList' => Currency::pluck('name', 'id'),
            'networkList' => getNetworkList()
        ];
    }

    public function update(Request $request, Room $room)
    {
        $room->fill($this->validateRoom())->save();
    }

    public function destroy(Room $room)
    {
        $room->delete();
    }

    public function getMobileRooms()
    {
        return Room::where('mobile', true)->get()->pluck('name', 'id');
    }

    public function getProxiesForRoom(Request $request)
    {
        $allproxies = Proxy::get(['id', 'name']);
        $networkId = Room::find(request('room_id'))->network_id;

        //Находим все аккаунты в этой сети и в $used_proxy_ids записываем id всех прокси, которые они использовали
        $accountsFromNetwork = Account::whereHas('room.network', function ($query) use($networkId) {
            return $query->where('network_id', $networkId);
        })->get();

        $proxyOfAccount = null;
        // Исключаем прокси самой модели
        if(request('account_id')) {
            $accountsFromNetwork = $accountsFromNetwork->reject(function ($item) {
                return $item->id == request('account_id');
            });
            // Находим проксик аккаунта чтобы добавить его позже если он в удаленных
            $proxyOfAccount = Account::find(request('account_id'))->proxy()->withTrashed()->first();
        }

        $used_proxy_ids = $accountsFromNetwork->map(function ($item) {
            return $item->historyProxies->pluck('id');
        })->flatten()->unique()->toArray();

        $filtered = $allproxies->filter(function ($item, $key) use($used_proxy_ids){
            // Исключаем проксики, уже использованные в данной лиге
            return !in_array($item->id, $used_proxy_ids);
        });

        if($proxyOfAccount && !$filtered->contains($proxyOfAccount->id)) {
            $filtered->push($proxyOfAccount);
        }
        $result = $filtered->values();

        return $result;
    }

    public function validateRoom()
    {
        return request()->validate(
            [
                'name' => 'required',
                'network_id' => 'required',
                'status_id' => 'required',
                'cashout_scenario' => 'required',
                'deposit_scenario' => 'required',
                'currency_id' => 'required',
                'mobile' => 'boolean',
                'info' => 'nullable'
            ],
            [
                'name.required' => "Безымянных румов не бывает",
                'network_id.required' => 'Рум должен принадлежать сети. Если сети нет в списке, то создайте сеть',
                'cashout_scenario.required' => 'Нужен сценарий кэшаута',
                'currency_id.required' => 'Основная валюта - обязательна',
            ]);
    }
}
