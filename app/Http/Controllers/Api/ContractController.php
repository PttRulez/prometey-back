<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Contract::with('profile')
            ->with('network')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:contracts,name',
            'network_id' => 'required|gt:0',
            'bob_id_id' => 'nullable'
        ]);

        return Contract::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Contract $contract
     * @return Contract
     */
    public function show(Contract $contract)
    {
        return $contract->load('network');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        request()->validate([
            'name' => 'required|unique:contracts,name,' . $id,
            'network_id' => 'required',
        ]);

        $contract = Contract::find($id);

        $contract->fill(request()->all())->save();
        return $contract;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contract $contract)
    {
        //
    }
}
