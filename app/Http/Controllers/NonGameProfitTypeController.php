<?php

namespace App\Http\Controllers;

use App\NonGameProfitType;
use Illuminate\Http\Request;

class NonGameProfitTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('non-game-profit-types.create')->with('model', new NonGameProfitType());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:non_game_profit_types',
            'info' => ''
        ]);

        NonGameProfitType::create($validated);

        return redirect()->route('settings')->with('status', 'Создан новый тип неигрового дохода');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NonGameProfitType  $nonGameProfitTypes
     * @return \Illuminate\Http\Response
     */
    public function show(NonGameProfitType $nonGameProfitTypes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NonGameProfitType  $nonGameProfitTypes
     * @return \Illuminate\Http\Response
     */
    public function edit(NonGameProfitType $nonGameProfitTypes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NonGameProfitType  $nonGameProfitTypes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NonGameProfitType $nonGameProfitTypes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NonGameProfitType  $nonGameProfitTypes
     * @return \Illuminate\Http\Response
     */
    public function destroy(NonGameProfitType $nonGameProfitTypes)
    {
        //
    }
}
