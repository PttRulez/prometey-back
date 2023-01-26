<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Account;
use App\Http\Requests\DepositRequest;

class DepositController extends Controller
{
    public function create()
    {
        return [
            'deposit' => new Deposit(['account_id' => request('account_id')]),
            'account' => Account::find(request('account_id')),
        ];
    }

    public function store(DepositRequest $request)
    {
        Deposit::create($request->validated());

    }

    public function edit(Deposit $deposit)
    {
        return [
            'deposit' => $deposit,
            'account' => $deposit->account
        ];
    }

    public function update(DepositRequest $request, Deposit $deposit)
    {
        $deposit->fill($request->validated())->save();

    }

    public function destroy(Deposit $deposit)
    {
        $deposit->delete();
    }
}
