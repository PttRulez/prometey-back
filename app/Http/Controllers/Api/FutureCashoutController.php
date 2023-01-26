<?php

namespace App\Http\Controllers\Api;

use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FutureCashout;

class FutureCashoutController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required',
            'account_id' => 'required',
            'date' => 'required',
        ]);

        $validated['user_id'] = auth()->user()->id;

        FutureCashout::create($validated);
    }

    public function create()
    {
        return Account::with('room.network')->find(request('account_id'));
    }

    public function show(FutureCashout $futureCashout)
    {
        return [
            'futureCashout' => $futureCashout,
            'account'   => $futureCashout->account
        ];
    }

    public function update(Request $request, FutureCashout $futureCashout)
    {
        $validated = $request->validate([
            'amount' => 'required',
            'account_id' => 'required',
            'date' => 'required',
        ]);

        $futureCashout->fill($validated)->save();
    }

    public function destroy(FutureCashout $futureCashout)
    {
        //
    }
}
