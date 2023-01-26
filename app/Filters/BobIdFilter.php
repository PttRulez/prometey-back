<?php

namespace App\Filters;

use App\Models\BobReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BobIdFilter extends QueryFilter
{
    public function bob_id($value)
    {
        if ($value) {
            $this->builder->where('bob_id', 'like', '%' . $value . '%');
        }
    }

    public function network_id($value)
    {
        if ($value) {
            $this->builder->where('network_id', $value);
        }
    }

    public function show_deleted($value)
    {
        if ($value) {
            $this->builder->onlyTrashed()->get();
        }
    }
}
