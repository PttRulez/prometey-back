<?php

namespace App\Filters;

use App\Models\BobReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProxyFilter extends QueryFilter
{
    public function name($value)
    {
        if ($value) {
            $this->builder->where('name', 'like', '%' . $value . '%');
        }
    }

    public function show_deleted($value)
    {
        if ($value) {
            $this->builder->onlyTrashed()->get();
        }
    }
}
