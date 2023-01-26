<?php

namespace App\Filters;

use App\Models\BobReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountFilter extends QueryFilter
{
    public function with_hands_for_last_month($value)
    {
        $this->builder
            ->whereHas('sessions', function ($query) use ($value) {
                $query->whereDate('created_at', '>=', Carbon::now()->subMonth($value)->startOfMonth()->toDateString());
            })
            ->withCount([
                'sessions AS hands' => function ($query) use ($value) {
                    $query->select(DB::raw("SUM(hands) as paidsum"))
                        ->whereDate('created_at', '>=', Carbon::now()->subMonth($value)->startOfMonth()->toDateString());
                }
            ])
            ->withCount([
                'bobReports AS total' => function ($query) use ($value) {
                    $query->select(DB::raw("SUM(total) as paidsum"))
                        ->where([
                            ['month', '>=', Carbon::now()->subMonth($value)->month],
                            ['year', '>=', Carbon::now()->subMonth($value)->year],
                            ['month', '!=', Carbon::now()->month]
                        ]);
                }
            ]);
    }

    public function network_id($value)
    {
        if ($value) {
            $this->builder->whereHas('room', function ($query) use ($value) {
                $query->where('network_id', $value);
            });
        }
    }

    public function affiliate_id($value)
    {

        if($value == 'no_aff') {
            $this->builder->where('affiliate_id', null);
        } elseif ($value) {
            $this->builder->where('affiliate_id', $value);
        }
    }

    public function bob_id($value)
    {
        if ($value) {
            $this->builder->whereHas('bobId', function ($query) use ($value) {
                $query->where('bob_id', 'like', '%' . $value . '%');
            });
        }
    }

    public function nickname($value)
    {
        if ($value) {
            $this->builder->where('nickname', 'like', '%' . $value . '%');
        }
    }

    public function login($value)
    {
        if ($value) {
            $this->builder->where('login', 'like', '%' . $value . '%');
        }
    }

    public function are_used_now($value)
    {
        if ($value) {
            $this->builder->stoppedOrActive();
        }
    }
}
