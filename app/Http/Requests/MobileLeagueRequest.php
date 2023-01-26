<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MobileLeagueRequest extends FormRequest
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
            'room_id' => 'required',
            'info' => 'nullable'
        ];

        if ($this->getMethod() == 'PATCH' || $this->getMethod() == 'PUT') {
            $rules += [
                'name' => ['required', Rule::unique('mobile_leagues')->ignoreModel($this->mobile_league)]

            ];
        } else if ($this->getMethod() == 'POST') {
            $rules += [
                'name' => 'required|unique:mobile_leagues',
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return
            [
                'name.required' => "Лиге нужно имя!",
                'room_id.required' => "Лиге нужен рум!"
            ];
    }
}
