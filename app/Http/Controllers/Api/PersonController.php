<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Person;

class PersonController extends Controller
{
    public function index(Request $request)
    {
        return $this->filter($request)->get();
    }

    public function store(Request $request)
    {
        $person = Person::create($this->validatePerson());
        return $person->id;
    }

    public function show(Person $person)
    {
        return $person->load('accounts.room.network');
    }

    public function update(Request $request, Person $person)
    {
        $person->fill($this->validatePerson())->save();
    }

    public function destroy(Person $person)
    {
        $person->delete();
    }

    public function validatePerson()
    {
        return request()->validate(
            [
                'name' => 'required',
                'email' => 'required|email',
                'email_password' => 'required',
                'secret_answer' => 'required',
                'phone' => 'required',
                'skype' => 'nullable',
                'address' => 'required',
                'zip' => 'required',
                'birthdate' => 'required',
                'btc_wallet' => 'nullable',
                'info' => 'nullable'
            ],
            [
                'name.required' => "Физику нужно имя!"
            ]);
    }

    public function filter(Request $request)
    {
        $sql = Person::with('accounts.room.network')->orderBy('name');

        if ($request->filled('name')) {
            $sql = $sql->where('name', 'like', '%' . $request['name'] .  '%');
        }

        if ($request->filled('phone')) {
            $sql = $sql->where('phone', 'like', '%' . $request['phone'] .  '%');
        }

        return $sql;
    }
}
