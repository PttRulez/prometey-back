<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MobileClubRequest extends FormRequest
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
            'club_id' => 'required',
            'agent_id' => 'nullable',
            'mobile_league_id' => 'required',
            'activity_status' => 'required',
            'chip_rate' => 'required',
            'limitations' => 'nullable',
            'info' => 'nullable'
        ];

        if ($this->getMethod() == 'PATCH' || $this->getMethod() == 'PUT') {
            $rules += [
                'name' => ['required', Rule::unique('mobile_clubs')->ignoreModel($this->mobile_club)]

            ];
        } else if ($this->getMethod() == 'POST') {
            $rules += [
                'name' => 'required|unique:mobile_clubs',
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return
            [
                'name.required' => "Клубу нужно имя!",
                'club_id' => 'Клубу нужен ID!',
                'mobile_league_id.required' => "Клубу нужна лига!",
                'chip_rate.required' => "Укажите, сколько стоит фишка в клубе",
            ];
    }
}
