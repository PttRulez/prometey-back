<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brain;
use \Illuminate\Http\Request;

class BrainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Brain::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:brains,name',
        ]);

        return Brain::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Brain $brain
     * @return Brain
     */
    public function show(Brain $brain)
    {
        return $brain;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Brain $brain
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        request()->validate([
            'name' => 'required|unique:brains,name,' . $id,
        ]);

        $brain = Brain::find($id);

        $brain->fill(request()->all())->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Brain $brain
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brain $brain)
    {
        //
    }
}
