<?php

namespace App\Http\Controllers\Api\BobReport;

use App\Http\Controllers\Controller;
use App\Models\BobReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankrollController extends Controller
{
    // Апдейт банкролла когда вводим цифру в отчёте
    public function editBankroll(Request $request)
    {
        $bobReport = BobReport::find($request->get('id'));

        $validated = $request->validate([
            $request['inputFieldName'] => 'required|integer|min:0'
        ]);

        $bobReport->fill($validated)->countTotalAndWin()->save();
        app('debugbar')->info($bobReport);
        return $bobReport;
    }

    // Апдейт начальных банкроллов всех отчётов месяца выбранного на странице отчёта
    public function updateMonthBankrollStart(Request $request)
    {
        $monthList = getMonthList();

        $reports = BobReport::where([
            'month' => $request->get('month'),
            'year' => $request->get('year')
        ])->get();

//        foreach ($reports as $report) {
//            $report->copyStartBankrollFromPrevious();
//        }

        DB::transaction(function () use ($reports) {
            $reports->each(function ($report) {
                $report->copyStartBankrollFromPrevious()
                    ->countTotalAndWin()
                    ->save();
            });
        });
        $reports->each(function ($item, $key) {
            $item->save();
        });

        return [
            'message' => 'В отчете за ' . $monthList[$request->get('month')]
                . ' ' . $request->get('year') . " БР вначале приравнялся к БР вконце прошлого месяца."
        ];
    }

    // Апдейт банкролла одного отчёта когда нажимаем календарик
    public function updateBankrollStart(Request $request)
    {
        $bobReport = BobReport::find($request->get('id'));

        $bobReport->copyStartBankrollFromPrevious()->Save();

        return $bobReport;
    }
}
