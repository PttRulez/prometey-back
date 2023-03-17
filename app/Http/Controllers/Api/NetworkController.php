<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Network;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    public function index()
    {
        return Network::orderBy('name')->get();
    }

    public function store(Request $request)
    {
        Network::create($this->validateNetwork());
        return redirect()->route('networks.index')->with('status', 'Создана новая сеть');
    }

    public function show(Network $network)
    {
        return $network;
    }

    public function update(Request $request, Network $network)
    {
        $network->fill($this->validateNetwork())->save();
        return $network->id;
    }

    public function destroy(Network $network)
    {
        $network->delete();
    }

    public function toggleTimetable($id)
    {
        $network = Network::find($id);
        $network->update(['profile_in_timetable' => !$network->profile_in_timetable]);
    }

    public function validateNetwork()
    {
        return request()->validate(
            [
                'name' => 'required',
                'info' => 'nullable'
            ],
            [
                'name.required' => "Сети нужно имя!"
            ]);
    }
}
