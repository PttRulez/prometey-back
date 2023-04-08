<?php

namespace App\Http\Controllers\Api;

use App\Filters\BobIdFilter;
use App\Http\Controllers\Controller;
use App\Models\BobId;
use App\Models\Network;
use Illuminate\Http\Request;

class BobIdController extends Controller
{
    public function index(Request $request, BobIdFilter $filters)
    {
        $bobIds = BobId::orderBy('bob_id')->with('network')
            ->with(['activeAccounts' => function ($q) {
                $q->with([
                    'room' => function ($q) {
                        $q->select('id', 'network_id');
                    },
                    'room.network' => function ($q) {
                        $q->select('id', 'name');
                    }
                ]);
            }])
            ->filter($filters)
            ->get();

        $networkIds = BobId::select('network_id')->distinct()->get()->toArray();
        $networkList = Network::whereIn('id', $networkIds)->get()->pluck('name', 'id');
        return compact('bobIds', 'networkList');
    }

    public function create()
    {
        return [
            'networkList' => Network::pluck('name', 'id')->all(),
            'existingLimits' => BobId::$existingLimits,
            'existingDisciplines' => BobId::$existingDisciplines,
        ];
    }

    public function store(Request $request)
    {
        $bobId = BobId::create($this->validateBobID());
        return $bobId->id;
    }

    public function show($id)
    {
        $bobId = BobId::withTrashed()->find($id);
        $bobId->load(['accounts.room.network', 'network', 'profile']);
        foreach ($bobId->activeAccounts as $account) {
            $account->load('room.network');
        }
        return $bobId;

    }

    public function edit(BobId $bobId)
    {
        return [
            'bobId' => $bobId,
            'networkList' => Network::pluck('name', 'id')->all(),
            'existingLimits' => BobId::$existingLimits,
            'existingDisciplines' => BobId::$existingDisciplines,
        ];
    }

    public function update(Request $request, BobId $bobId)
    {
        $bobId->fill($this->validateBobID($bobId->id))->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $proxy
     * @return \Illuminate\Http\Response
     */
    public function destroy(BobId $bobId)
    {
        $bobId->delete();
    }

    public function restore($id)
    {
        $bobId = BobId::withTrashed()->find($id);
        $bobId->restore();
    }

    public function validateBobID($bobId = '')
    {
        return request()->validate(
            [
                'bob_id' => 'required|integer',
                'network_id' => 'required',
                'disciplines' => 'required',
                'limits' => 'required',
                'profile_id' => 'required'
//                'profile_id' => 'nullable|unique:bob_ids,profile_id,' . $bobId
            ],
            [
                'bob_id.required' => "Введите номер Bob ID",
                'network_id.required' => 'Bob ID должен принадлежать сети. Если сети нет в списке, то создайте сеть',
                'disciplines.required' => 'Заполните дисциплины',
                'limits.required' => 'Заполните лимиты',
                'profile_id.required' => 'Выберите профиль',
            ]);
    }

    public function getByNetworkId($id)
    {
        return BobId::where(['network_id' => $id])->get();
    }
}
