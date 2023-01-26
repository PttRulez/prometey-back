<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProxyProvider;

class ProxyProviderController extends Controller
{
    public function index()
    {
        return ProxyProvider::all();
    }

    public function store(Request $request)
    {
        $provider = ProxyProvider::create($this->validateProxyProvider());
        return $provider->id;
    }

    public function show(ProxyProvider $proxyProvider)
    {
        return $proxyProvider;
    }

    public function update(Request $request, ProxyProvider $proxyProvider)
    {
        $proxyProvider->fill($this->validateProxyProvider())->save();
    }

    public function destroy(ProxyProvider $proxyProvider)
    {
        $proxyProvider->delete();
    }

    public function validateProxyProvider()
    {
        return request()->validate(
            [
                'name' => 'required',
                'info' => 'required'
            ],
            [
                'name.required' => "Провайдеру нужно имя!",
                'info.required' => "По провайдеру нужна как минимум инфа о контактных данных типа сайта. А также логине и пароле"
            ]);
    }
}
