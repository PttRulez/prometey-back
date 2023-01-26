<?php

namespace App\Http\Controllers\Api;

use App\Models\ProxyProvider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Proxy;
use App\Http\Requests\ProxyRequest;
use App\Filters\ProxyFilter;

class ProxyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ProxyFilter $filters)
    {
        return Proxy::with([
            'historyAccounts.room.network',
            'mobileAccounts.mobileClub.mobileLeague.room',
            'proxyProvider',
            ])
            ->orderBy('name')->filter($filters)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return ProxyProvider::orderBy('name')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProxyRequest $request)
    {
        return Proxy::create($request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Proxy
     */
    public function show($id)
    {
        $proxy= Proxy::withTrashed()->find($id);
        return $proxy->load([
            'historyAccounts.room.network',
            'mobileAccounts.mobileClub.mobileLeague.room',
            'proxyProvider',
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Proxy $proxy
     * @return array
     */
    public function edit(Proxy $proxy)
    {
        return [
            'model' => $proxy,
            'providersList' => ProxyProvider::orderBy('name')->get()
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProxyRequest $request, Proxy $proxy)
    {
        $proxy->fill($request->validated())->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $proxy
     * @return \Illuminate\Http\Response
     */
    public function destroy(Proxy $proxy)
    {
        $proxy->delete();
    }

    public function restore($id)
    {
        $proxy= Proxy::withTrashed()->find($id);
        $proxy->restore();
    }
}
