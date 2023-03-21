<?php

namespace App\Filters;

class ProfileFilter extends QueryFilter
{
    public function network_id($value)
    {
        if ($value) {
            $this->builder->whereHas('contract', function ($query) use ($value) {
                $query->where('network_id', $value);
            });
        }
    }
}
