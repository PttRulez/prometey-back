<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use Carbon\Carbon;
use App\Models\CurrencyRate;

class CurrencyController extends Controller
{
    public function index()
    {
        $year = request('year') ?? Carbon::now()->year;

        $models = Currency::with(['currency_rates' => function ($query) use ($year) {
            $query->where('year', $year);
        }])->get()->map(function($item) {
            $item->currencyRates = $item->currency_rates->keyBy('month');
            return $item;
        });

        return $models;
    }

    public function show(Currency $currency)
    {
        return $currency;
    }

    public function store(Request $request)
    {
        Currency::create($this->validateCurrency());
        return redirect()->route('currencies.index')->with('status', 'Новая валюта добавлена');
    }

    public function update(Request $request, Currency $currency)
    {
        $currency->fill($this->validateCurrency())->save();

    }

    public function destroy(Currency $currency)
    {
        $currency->delete();

    }

    public function createRate(Currency $currency)
    {
        return [
            'currency' => $currency,
            'monthList' => getMonthList(),
            'yearList' => getYearList(),
        ];
    }

    public function storeRate()
    {
        $exists = CurrencyRate::where([
            'year' => request('year'),
            'month' => request('month'),
            'currency_id' => request('currency_id'),
        ])->first();

        if ($exists) {
            $exists->fill($this->validateCurrencyRate())->save();
            return;
        }

        CurrencyRate::create($this->validateCurrencyRate());
    }

    public function validateCurrency()
    {
        return request()->validate(
            [
                'name' => 'required',
            ],
            [
                'name.required' => "Введите название валюты"
            ]);
    }

    public function validateCurrencyRate()
    {
        return request()->validate(
            [
                'currency_id' => 'required',
                'year' => 'required',
                'month' => 'required',
                'rate_to_usd' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            ],
            [
                'year.required' => "Выберите год",
                'month.required' => "Выберите месяц",
                'rate_to_usd.required' => "Введите курс валюты",
                'rate_to_usd.regex' => "Десятичные части курса - через точку"
            ]);
    }
}
