<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SessionController extends Controller
{
    public function getSessionsJson(Request $request)
    {
        $sessions = Session::where('finish', $request->get('finished'))
            ->orderBy('created_at', 'desc')->get()
            ->map(function ($item, $key)
            {
                if($item->hasProblems()) {
                    $item['hasProblems'] = true;
                }
                $item['accountLink'] = route('accounts.show', $item->account_id);
                $item['startTime'] = $item->created_at
                    ->locale('ru')->addHours(3)->isoFormat('D MMMM - HH:mm:ss');
                $item['finishTime'] = $item->updated_at->addHours(3)
                    ->locale('ru')->isoFormat('D MMMM - HH:mm:ss');
                return $item;
            })->toArray();

        return $sessions;
    }
}
