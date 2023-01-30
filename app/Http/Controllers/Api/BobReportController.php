<?php

namespace App\Http\Controllers\Api;

use App\Filters\BobReportFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\BobReportRequest;
use App\Http\Resources\BobReportResource;
use App\Models\BobReport;
use App\Models\Cashout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BobReportController extends Controller
{
    public function index(Request $request, BobReportFilter $filters)
    {

        $fils = json_decode($request->get('filters'));
        Log::info(["BobReports request filters", $fils]);
        $reports = BobReport::with('account.room.network', 'account.bobId', 'account.brain')
            ->with(['account.deposits' => function ($q) use ($fils) {
                $q->whereMonth('reached_balance_date', '=', $fils->month)
                    ->whereYear('reached_balance_date', '=', $fils->year);
            }])
            ->with(['account.cashouts' => function ($q) use ($fils) {
                $q->where('type_id', Cashout::TYPE_FROM_LOBBY)
                    ->whereMonth('left_balance_date', '=', $fils->month)
                    ->whereYear('left_balance_date', '=', $fils->year);
            }])
            ->with(['account.nonGameProfits' => function ($q) use ($fils) {
                $q->whereMonth('date', '=', $fils->month)
                    ->whereYear('date', '=', $fils->year);
            }])
            ->filter($filters)->get()
            ->map(function ($item, $key) {
                if ($item->account->room->network_id)
                    $item->network_id = $item->account->room->network_id;
                $item->deposits = $item->account->deposits;
                $item->depositsSum = $item->account->deposits->sum('amount');
                $item->cashoutsSum = $item->account->cashouts->sum('amount');
                $item->ngpSum = $item->account->nonGameProfits->sum('amount');
                return $item;
            });

        $networkList = BobReport::with(['account.room.network'])->where([
            'year' => $fils->year,
            'month' => $fils->month
        ])
            ->get()->pluck('account.room.network')->unique()->sortBy('name')->pluck('name', 'id');

        return ['reports' => BobReportResource::collection($reports), 'networkList' => $networkList];
    }

    public function store(BobReportRequest $request)
    {
        $validatedData = $request->validated();

        BobReport::create($validatedData);
    }

    public function show(BobReport $bobReport)
    {
        $bobReport->cashouts = $bobReport->cashoutsForReport();
        $bobReport->deposits = $bobReport->deposits();

        return [
            'model' => $bobReport->load('account.room.network'),
        ];
    }

    public function update(BobReportRequest $request, BobReport $bobReport)
    {
        $validated = $request->validated();

        $bobReport->fill($validated)->save();
    }

    public function destroy(BobReport $bobReport)
    {
        $bobReport->delete();
    }

    public function bobReportRedirect(Request $request)
    {
        $report = BobReport::where([
            'account_id' => $request['accountId'],
            'year' => $request['year'],
            'month' => $request['month'],
        ])->first();

        if ($report)
            return $report->id;
        else
            return 'no';
    }
}
