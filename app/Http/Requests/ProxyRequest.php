<?php

namespace App\Http\Requests;

use App\Models\Proxy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class ProxyRequest extends FormRequest
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
        return
            [
                'name' => 'required',
                'ip_port' => ['required',
                    function ($attribute, $value, $fail) {
                        // Проверяем уникальность проксика
                        $sql = Proxy::withTrashed()->where('ip_port', $value);
                        if ($this->method() != "POST") {
                            $sql = $sql->where('id', '!=', $this->route('proxy')->id);
                        }
                        $duplicate = $sql->first();

                        if ($duplicate) {
                            $fail('Уже есть проксик ' . $duplicate->name . ' с таким же айпи-портом ' . $value);
                        }

                    }],
                'authentication' => 'required',
                'proxy_provider_id' => 'required',
                'active'    => 'boolean'
            ];
    }

    public function messages()
    {
        return
            [
                'name.required' => "Нужно дать уникальное имя проксику",
                'ip_port.required' => 'Проксик должен быть с ip и портом',
                'authentication.required' => 'Данные аутентификации дял прокси тоже нужны',
                'proxy_provider_id.required' => 'У прокси обязательно долен быть провайдер, заведенный на сайте',

            ];
    }
}
