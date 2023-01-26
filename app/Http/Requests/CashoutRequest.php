<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\Cashout;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CashoutRequest extends FormRequest
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
        $cashout = new Cashout($this->input());

        $rules = [
            'amount' => 'required|integer',
            'account_id' => 'required',
            'status_id' => 'required',
            'type_id' => 'required',
        ];

        if ($cashout->goesToMain() || $cashout->instant()  || $cashout->comesBackIfCanceled()) {
            $rules['left_balance_date'] = 'required';
        }

        if ($cashout->orderFromAff() || $cashout->goesFromMain()) {
            $rules = array_replace($rules, ['left_balance_date' => 'nullable']);
            $rules = array_replace($rules, ['ordered_date' => 'required']);
        }
        if ($cashout->orderFromAff() || $cashout->left_balance_date)
             $rules = array_replace($rules, ['left_balance_date' => 'after_or_equal:ordered_date']);
        if ($cashout->orderFromAff())
            $rules = array_replace($rules, ['left_balance_date' => 'required_if:status_id,' . Cashout::STATUS_SUCCESS]);
        if ($cashout->comesBackIfCanceled() || $cashout->returned_balance_date)
             $rules = array_replace($rules, ['returned_balance_date' => 'after_or_equal:left_balance_date']);

        return $rules;
    }

    public function messages()
    {
        return
            [
                'required' => 'Не заполнено поле :attribute',
                'after_or_equal' => "\":attribute\" должна быть такая же или позже, чем \":date\"",
                'left_balance_date.required_if' => 'Если афф уже отправил деньги, то нужна дата, когда они ушли с баланса рума'
            ];
    }

    public function attributes()
    {
        return [
            'amount' => 'СУММА',
            'status_id' => 'СТАТУС',
            'ordered_date' => 'Дата, когда заказан',
            'left_balance_date' => 'Дата, когда ушел с баланса',
            'canceled_date' => 'Дата, когда отменили и вернули на баланс в лобби',
        ];
    }
}
