<?php

namespace App\Http\Requests;

use App\Models\BobReport;
use Illuminate\Foundation\Http\FormRequest;

class BobReportRequest extends FormRequest
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
        $rules = [
            'year' => 'required',
            'month' => 'required',
            'currency_id' => 'sometimes',
            'bankroll_start' => 'integer',
            'bankroll_finish' => 'integer',
            'total' => 'integer',
            'win' => 'integer'
        ];

        if ($this->getMethod() == 'PATCH' || $this->getMethod() == 'PUT')
            $rules += ['account_id' => 'required'];

        if ($this->getMethod() == 'POST') {
            $rules += [
                'account_id' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        // Проверяем на повтор. Запись в отчете должна быть уникально по accoun_id, year, month
                        $repeat = BobReport::where([
                            ['account_id', '=', $this->input('account_id')],
                            ['year', '=', $this->input('year')],
                            ['month', '=', $this->input('month')],
                            ['id', '!=', $this->input('id')]
                        ])->get();
                        if ($repeat->count() > 0) {
                            $fail('Для этого аккаунта уже есть запись в отчете для этих месяца и года');
                        }
                    }
                ]
            ];
        }

        return $rules;
    }
}
