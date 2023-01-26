<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BobId;
use App\Models\Session;
use App\Models\SessionLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SessionLogController extends Controller
{
    public function fake()
    {
        return view('session.form');
    }

    public function store(Request $request)
    {
        $session = Session::getSession($request);
        if (!$session) {
            Session::makeNewSession($request);
        } else {
            $session->updateSession($request);
        }
    }
}
