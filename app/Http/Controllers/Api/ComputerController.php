<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Computer;

class ComputerController extends Controller
{
    public function index()
    {
        return Computer::all();
    }

    public function show(Computer $computer)
    {
        return $computer;
    }

    public function store(Request $request)
    {
        Computer::create($request->validate(['name' => 'required|unique:computers']));
    }

    public function update(Request $request, Computer $computer)
    {
        $computer->fill($request->validate(['name' => 'required|unique:computers']))->save();
    }

    public function destroy(Computer $computer)
    {
        $computer->delete();
    }
}
