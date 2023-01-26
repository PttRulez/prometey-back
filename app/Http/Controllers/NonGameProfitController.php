<?php

namespace App\Http\Controllers;

use App\Account;
use App\NonGameProfit;
use App\NonGameProfitType;
use Illuminate\Http\Request;

class NonGameProfitController extends Controller
{
    public function create()
    {
         return view('non-game-profits.create')->with([
             'model' => new NonGameProfit(),
             'account' => Account::find(request('account_id')),
             'nonGameProfitTypes' => NonGameProfitType::pluck('name', 'id')
         ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required',
            'type_id' => 'required',
            'account_id' => 'required',
            'date' => 'date:Y-m-d'
            ]);
        NonGameProfit::create($validated);

        return redirect()->route('bob-reports.index')->with('status', 'Неигровой доход добавлен');
    }


    public function edit(NonGameProfit $nonGameProfit)
    {
        return view('non-game-profits.edit')->with([
             'model' => $nonGameProfit,
             'account' => $nonGameProfit->account,
             'nonGameProfitTypes' => NonGameProfitType::pluck('name', 'id')
         ]);
    }

    public function update(Request $request, NonGameProfit $nonGameProfit)
    {
        $validated = $request->validate([
            'amount' => 'required',
            'type_id' => 'required',
            'account_id' => 'required',
            'date' => 'date:Y-m-d'
            ]);

        $nonGameProfit->fill($validated)->save();

        return redirect()->route('bob-reports.index')->with('status', 'Данные изменены');

    }

}
