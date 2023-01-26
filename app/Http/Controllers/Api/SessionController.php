<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function finishSession($id)
    {
        Session::find($id)->update(['finish' => 1]);
    }
}
