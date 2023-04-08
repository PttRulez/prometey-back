<?php

namespace App\Http\Controllers\Api;

use App\Filters\ProfileFilter;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index(ProfileFilter $filters)
    {
        return Profile::with('contract.network')
            ->with(['bobId' => function ($q) {
                return $q->with('activeAccounts');
            }])
            ->with(['accounts' => function ($q) {
                return $q->where('status_id', '<=', Account::STATUS_STOPPED);
            }])
            ->filter($filters)
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => Rule::unique('profiles', 'name')->where(function ($q) {
                Log::info(request('contract_id'));
                $q->where('contract_id', request('contract_id'));
            }),
            'contract_id' => 'required',
            "shift_id" => 'nullable',
            'limits' => 'required',
            'disciplines' => 'required',
        ]);

        return Profile::create($request->all());
    }

    public function show(Profile $profile)
    {
        return $profile->load('contract');
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => Rule::unique('profiles', 'name')->where(function ($q) {
                Log::info(request('contract_id'));
                $q->where('contract_id', request('contract_id'));
            })->ignore($id),

            'contract_id' => 'required',
            'shift_id' => 'nullable',
            'limits' => 'required',
            'disciplines' => 'required',
        ];
        $messages = [
            'name.unique' => 'Уже есть профиль с таким названием и таким же контрактом',
        ];
        request()->validate($rules, $messages);

        $contract = Profile::find($id);

        $contract->fill(request()->all())->save();
        return $contract;
    }

    public function getByNetworkId($id)
    {
        return Profile::whereHas('contract', function ($q) use ($id) {
            $q->where('network_id', ($id));
        })->get()->filter(function ($profile) {
            return (!$profile->bobId || $profile->bobId->id == request('including_bob_id_id'));
        })->values();
    }

    public function getAllProfilesLists()
    {
        return Profile::with(['bobId.accounts', 'contract'])->get()->groupBy('contract.network_id');
    }
}


