<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return Profile::with('contract.network')
            ->with(['bobId' => function ($q) {
                return $q->with('activeAccounts');
            }])
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:profiles,name',
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

    public function update($id)
    {
        request()->validate([
            'name' => 'required|unique:profiles,name,' . $id,
            'contract_id' => 'required',
            'shift_id' => 'nullable',
            'limits' => 'required',
            'disciplines' => 'required',
        ]);

        $contract = Profile::find($id);

        $contract->fill(request()->all())->save();
        return $contract;
    }

    public function getByNetworkId($id)
    {
        return 'privet';
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


