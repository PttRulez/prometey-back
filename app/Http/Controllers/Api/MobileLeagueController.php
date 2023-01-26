<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MobileLeague;
use App\Models\Room;
use App\Http\Requests\MobileLeagueRequest;

class MobileLeagueController extends Controller
{
    public function index()
    {
        return MobileLeague::with('room')->get();
    }

    public function show(MobileLeague $mobileLeague)
    {
        return $mobileLeague;
    }


    public function store(MobileLeagueRequest $request)
    {
        MobileLeague::create($request->validated());
    }

    public function update(MobileLeagueRequest $request, MobileLeague $mobileLeague)
    {
        $mobileLeague->fill($request->validated())->save();
    }

    public function destroy(MobileLeague $mobileLeague)
    {
        $mobileLeague->delete();
    }
}
