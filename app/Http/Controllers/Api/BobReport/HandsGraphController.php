<?php

namespace App\Http\Controllers\Api\BobReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BobReport;
use Carbon\Carbon;
use App\Models\Session;

class HandsGraphController extends Controller
{
    public function handsChartData(Request $request)
    {
        $monthList = getMonthList();

        $result = BobReport::all()->groupBy(function ($item, $key) use ($monthList) {
            return $monthList[$item['month']] . ' ' . getYearList()[$item['year']];
        })->map(function ($item, $key) {
            return $item->reduce(function ($carry, $item) {
                return $carry + $item->hands_played;
            }, 0);
        });

        $profitsArray = $result->toArray();

        return [
            'labels' => array_keys($profitsArray),
            'datasets' => array([
                'label' => 'Кол-во сыгранных раздач по месяцам',
                'backgroundColor' => 'blue',
                'borderColor' => 'blue',
                'data' => array_values($profitsArray)
            ])
        ];
    }

    public function handsByDayChartData(Request $request)
    {
        $handsArray = Session::where('created_at', '>', Carbon::today()->subDays(30))->get()
            ->groupBy('day')
            ->map(function ($item, $k) {
                return $item->sum('hands');
            })
            ->toArray();

        return [
            'labels' => array_keys($handsArray),
            'datasets' => array([
                'label' => 'Кол-во сыгранных раздач по дням',
                'backgroundColor' => 'blue',
                'borderColor' => 'blue',
                'data' => array_values($handsArray)
            ])
        ];
    }

    public function networksHandsChartData(Request $request)
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

            $totalHandsByMonths = $results->groupBy(function ($item, $key) {
                return getMonthList()[$item['month']] . ' ' . $item['year'];
            })->map(function ($item, $key) {
                return $item->reduce(function ($carry, $item) {
                    return $carry + $item->hands_played;
                }, 0);
            })->toArray();

            $data = [];

            foreach ($monthNames as $index => $monthName) {
                $totalHands = $totalHandsByMonths[$monthName] ?? 0;
                $data[$index] = $totalHands;
            }


            array_push($datasets, [
                'label' => $networkName,
                'borderColor' => $randomColor,
                'fill' => false,
                'data' => $data,
                'hidden' => true,
            ]);
        }
        $result = [
            'labels' => $monthNames,
            'datasets' => $datasets
        ];

        return [
            'labels' => $monthNames,
            'datasets' => $datasets
        ];
    }
}
