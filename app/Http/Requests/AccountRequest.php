<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\BobId;
use App\Models\Person;
use App\Models\Proxy;
use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountRequest extends FormRequest
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
            'room_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Проверяем входит ли рум в ту же сеть, что и Bob ID
                    $room = Room::find($value);
                    $bobId = BobId::withTrashed()->find($this->input('bob_id_id'));
                    if ($bobId && ($room->network_id != $bobId->network_id)) {
                        $fail('Рум и Bob ID должны быть из одной сети. Выбран рум ' . $room->name . ' из сети ' .
                            $room->network->name . ', а Bob ID относится к сети ' . $bobId->network->name);
                    }
                }],
            'person_id' => function ($attribute, $value, $fail) {
                if ($value && !empty($this->input('room_id'))) {
                    $id = $this->route('account')->id ?? null;
                    $person = Person::find($value);
                    $accounts = $person->accounts;
                    $network_id = Room::find($this->input('room_id'))->network_id;
                    foreach ($accounts as $account) {
                        // Если среди акков на этого физика есть акк той же сети (исключаем самого себя (если update)), то FAIl
                        if ($account->room->network_id == $network_id &&
                            $account->id != $id)
                            $fail('На физика ' . $person->name . ' уже есть акк ' . $account->nickname . ' в этой сети ');
                    }
                }

            },
            'brain_id' => 'required|integer',
            'bob_id_name' => 'nullable',
            'bob_id_id' => 'nullable',
            'disciplines' => '',
            'limits' => '',
            'limits_group' => '',
            'profile_id' => 'nullable',
            'affiliate_id' => 'nullable',
            'shift_id' => 'required',
            'currency_id' => '',
            'login' => 'required',
            'password' => 'required',

            'info' => '',
            'status_id' => ['required',
                function ($attribute, $value, $fail) {
                    // Проверяем входит ли рум в ту же сеть, что и Bob ID
                    if ($value != Account::STATUS_ACTIVE && !$this->input('comment')) {
                        $fail("Напишите комментарий, почему статус не \"Активен\"");
                    }
                }],
            'comment' => '',
            'creation_date' => 'required',
            'created_by' => '',
        ];

        if ($this->getMethod() == 'PATCH' || $this->getMethod() == 'PUT') {
            $rules += [
                'nickname' => ['required', Rule::unique('accounts')->ignoreModel($this->account)],
                'proxy_id' => [
                    'nullable',
                    function ($attribute, $value, $fail) {
                        if ($value) {
                            $proxy = Proxy::withTrashed()->with('historyAccounts.room.network')->find($value);

                            if (!$proxy->historyAccounts)
                                return;

                            foreach ($proxy->historyAccounts as $account) {

                                if ($this->route('account')->id == $account->id)
                                    continue;
                                if ($account->room->network->id == $this->route('account')->room->network->id)
                                    $fail($account->nickname . ' из сети ' . $account->room->network->name . ' использовал(ет) прокси ' . $proxy->name);
                            }
                        }

                    }
                ],
            ];
        }

        if ($this->getMethod() == 'POST') {
            $rules += [
                'nickname' => 'required|unique:accounts',
                'proxy_id' => [
                    'nullable',
                    function ($attribute, $value, $fail) {
                        if ($value) {
                            $proxy = Proxy::with('historyAccounts.room.network')->find($value);

                            if (!$proxy->historyAccounts)
                                return;
                            $network = Room::find(request('room_id'))->network;
                            foreach ($proxy->historyAccounts as $account) {
                                if ($account->room->network->id == $network->id)
                                    $fail($account->nickname . ' из сети ' . $account->room->network->name . ' использовал(ет) прокси ' . $proxy->name);
                            }
                        }

                    }
                ],
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return
            [
                'required' => 'Не заполнено поле :attribute',
            ];
    }

    public function attributes()
    {
        return [
            'room_id' => 'РУМ',
            'nickname' => 'НИКНЕЙМ',
            'person_id' => 'ФИЗИК',
            'bob_id' => 'Bob ID',
            'disciplines' => 'Играемые дисциплины',
            'limits' => 'Играемые лимиты',
            'limits_group' => 'Лимитная группа',
            'shift_id' => 'СМЕНА',
            'affiliate_id' => 'Аффилейт',
            'login' => 'ЛОГИН',
            'password' => 'ПАРОЛЬ',
            'proxy_id' => 'ПРОКСИ',
            'status_id' => 'СТАТУС',
            'comment' => 'Комментарий',
            'creation_date' => 'Дата создания аккаунта',
        ];
    }
}
