<?php

namespace App\Filters;

use App\BobReport;

class BobReportFilter extends QueryFilter
{
    public function year($value)
    {
        $this->builder->where('year', $value);

    }

    public function brain_id($value)
    {
        if ($value) {
            $this->builder->whereHas('account', function ($query) use ($value) {
                $query->where('brain_id', $value);
            });
        }
    }

    public function month($value)
    {
        $this->builder->where('month', $value);
    }

    public function network_id($value)
    {
        if ($value) {
            $this->builder->whereHas('account', function ($query) use ($value) {
                $query->whereHas('room', function ($query) use ($value) {
                    $query->where('network_id', $value);
                });
            });
        }
    }

    public function nickname($value)
    {
        $this->builder->whereHas('account', function ($query) use ($value) {
            $query->where('nickname', 'like', '%' . $value . '%');
        });
    }
}
