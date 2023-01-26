<?php

namespace App\Http\Controllers\Api\BobReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BobReport;

class ProfitGraphController extends Controller
{
    public function profitChartData(Request $request)
    {
        $monthList = getMonthList();

        $result = BobReport::all()->groupBy(function ($item, $key) use ($monthList) {
            return $monthList[$item['month']] . ' ' . getYearList()[$item['year']];
        })->map(function ($item, $key) {
            return $item->reduce(function ($carry, $item) {
                return $carry + $item->total;
            }, 0);
        });

        $profitsArray = $result->toArray();

        return [
            'labels' => array_keys($profitsArray),
            'datasets' => array([
                'label' => 'Профит по месяцам',
                'backgroundColor' => '#F26202',
                'borderColor' => 'blue',
                'data' => array_values($profitsArray)
            ])
        ];
    }

    public function networksProfitChartData(Request $request)
    {
        $monthList = getMonthList();

        $reportsWIthMonthNames = BobReport::all()->map(function ($item, $key) use ($monthList) {
            $item->monthName = $monthList[$item['month']] . ' ' . $item['year'];
            return $item;
        });

        $monthNames = $reportsWIthMonthNames->unique('monthName')->pluck('monthName')->toArray();

        $networksResults = $reportsWIthMonthNames->groupBy(function ($item, $key) {
            return $item->account->network->name;
        });

        $datasets = [];

        foreach ($networksResults as $networkName => $results) {
            $randomColor = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);

            $totalProfitByMonths = $results->groupBy(function ($item, $key) {
                return getMonthList()[$item['month']] . ' ' . $item['year'];
            })->map(function ($item, $key) {
                return $item->reduce(function ($carry, $item) {
                    return $carry + $item->total;
                }, 0);
            })->toArray();

            $data = [];

            foreach ($monthNames as $index => $monthName) {
                $total = $totalProfitByMonths[$monthName] ?? 0;
                $data[$index] = $total;
            }


            array_push($datasets, [
                'label' => $networkName,
                'borderColor' => $randomColor,
                'fill' => false,
                'data' => $data,
                'hidden' => true,
            ]);
        }

        return [
            'labels' => $monthNames,
            'datasets' => $datasets
        ];
    }
}
