<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cashout;
use App\Models\Account;
use App\Http\Requests\CashoutRequest;
use App\Http\Resources\CashoutResource;
use Carbon\Carbon;

class CashoutController extends Controller
{
    public function create()
    {
        $cashout = new Cashout(['account_id' => request('account_id')]);
        $cashout->goesToMain = $cashout->goesToMain();
        $cashout->comesBackifCanceled = $cashout->comesBackifCanceled();
        $cashout->instant = $cashout->instant();
        $cashout->goesFromMain = $cashout->goesFromMain();
        $cashout->orderFromAff = $cashout->orderFromAff();

        return [
            'cashout' => $cashout,
            'account' => Account::with('room.network')->find(request('account_id'))
        ];
    }

    public function store(CashoutRequest $request)
    {
        $cashout = new Cashout(['account_id' => $request->get('account_id')]);
        $validated = $request->validated();

        $account = Account::find($request->get('account_id'));
        $futureCashouts = $account->cashouts->filter(function ($item, $key) {
           return $item->isFuture();
        });

        foreach($futureCashouts as $cashout) {
            $cashout->delete();
        }

        // Создается кэшаут из лобби и сразу автоматом с мэйна. Тайгер
        if ($cashout->goesToMain()) {
            Cashout::create($validated);
            Cashout::create(array_merge($validated, [
                'ordered_date' => $validated['left_balance_date'],
                'left_balance_date' => null,
                'type_id' => Cashout::TYPE_FROM_MAIN,
                'status_id' => Cashout::STATUS_PENDING,
            ]));
        }

        // Создается просто один новый кэшаут с мэйна. Тайгер
        if ($cashout->goesFromMain()) {
            // Создается кэшаут из  мэйна
            Cashout::create(array_merge($validated, [
                'type_id' => Cashout::TYPE_FROM_MAIN,
                'status_id' => Cashout::STATUS_PENDING,
            ]));
        }

        if ($cashout->instant()) {
            Cashout::create(array_merge($validated, [
                'status_id' => Cashout::STATUS_SUCCESS
            ]));
        }

        // Создается один кэшаут
        if ($cashout->orderFromAff() || $cashout->comesBackIfCanceled()) {
            Cashout::create($validated);
        }

        return [ 'message' => 'Кэшаут добавлен'];
    }

    public function edit(Cashout $cashout)
    {
        return [
            'cashout' => new CashoutResource($cashout),
            'statusList' => Cashout::statusList(),
            'account' => $cashout->account->load('room.network')
        ];
    }

    public function update(CashoutRequest $request, Cashout $cashout)
    {
        $validated = $request->validated();

        if (($cashout->goesFromMain() || $cashout->orderFromAff()) && $request->left_balance_date)
            $validated['status_id'] = Cashout::STATUS_SUCCESS;

        if (($cashout->comesBackIfCanceled() && $request->returned_balance_date)) {
            $validated['status_id'] = Cashout::STATUS_CANCELED;
        }

        $cashout->fill($validated)->save();
    }

    public function destroy(Cashout $cashout)
    {
        $cashout->delete();
    }

    public function success(Cashout $cashout)
    {
        $cashout->status_id = Cashout::STATUS_SUCCESS;
        $cashout->left_balance_date = $cashout->left_balance_date ?? Carbon::now();
        $cashout->save();
    }
}
