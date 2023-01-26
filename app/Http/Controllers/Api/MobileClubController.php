<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MobileClub;
use App\Http\Requests\MobileClubRequest;
use App\Models\MobileLeague;

class MobileClubController extends Controller
{
    public function index()
    {
        return MobileClub::with('mobileLeague')->get();
    }

    public function create()
    {
        return [
            'mobileLeagues' => MobileLeague::all()->pluck('name', 'id')
        ];
    }

    public function store(MobileClubRequest $request)
    {
        MobileClub::create($request->validated());
    }

    public function show(MobileClub $mobileClub)
    {
        return $mobileClub->load('mobileLeague');
    }

    public function edit (MobileClub $mobileClub)
    {
        return [
            'model' => $mobileClub->load('mobileLeague'),
            'mobileLeagues' => MobileLeague::all()->pluck('name', 'id')
        ];
    }

    public function update(MobileClubRequest $request, MobileClub $mobileClub)
    {
        $mobileClub->fill($request->validated())->save();
    }

    public function destroy(MobileClub $mobileClub)
    {
        $mobileClub->delete();
    }
}
