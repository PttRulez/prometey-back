<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Deposit;

class DepositRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $deposit = new Deposit($this->input());

        $rules = [
            'account_id' => 'required',
            'amount' => 'required',
            'comment' => 'present',
        ];

        if ($deposit->orderFromAff()) {
            $rules = array_replace($rules, ['ordered_date' => 'required']);
            if ($this->input('reached_balance_date')) {
                // Если заполнено поле даты поступления денег на акк, то эта дата должна быть больше чем дата заказа
                $rules = array_replace($rules, ['reached_balance_date' => 'after_or_equal:ordered_date']);
            } else {
                $rules = array_replace($rules, ['reached_balance_date' => 'nullable']);
            }
        } else {
            $rules = array_replace($rules, ['reached_balance_date' => 'required']);
        }


        return $rules;
    }

    public function messages()
    {
        return
            [
                'required' => 'Не заполнено поле :attribute',
                'after_or_equal' => "\":attribute\" должна быть такая же или позже, чем \":date\"",
            ];
    }

    public function attributes()
    {
        return [
            'amount' => 'СУММА',
            'ordered_date' => 'ДАТА, КОГДА ЗАКАЗАН',
            'reached_balance_date' => 'ДАТА, КОГДА ДЕНЬГИ ПОСТУПИЛИ НА БАЛАНС',
        ];
    }
}
