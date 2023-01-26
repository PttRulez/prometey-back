<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Affiliate;

class AffiliateController extends Controller
{
    public function index()
    {
        return Affiliate::all();
    }

    public function store(Request $request)
    {
        $aff = Affiliate::create($this->validateAffiliate());
        return $aff->id;
    }

    public function show(Affiliate $affiliate)
    {
        return $affiliate;
    }

    public function update(Request $request, Affiliate $affiliate)
    {
        $affiliate->fill($this->validateAffiliate())->save();
    }

    public function destroy(Affiliate $affiliate)
    {
        $affiliate->delete();
    }

    public function validateAffiliate()
    {
        return request()->validate(
            [
                'name' => 'required',
                'contact' => 'required',
                'info' => 'nullable'
            ],
            [
                'name.required' => "Аффу нужно имя!",
                'contact.required' => "Введите какой-то контакт аффа"
            ]);
    }
}
