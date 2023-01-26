<?php

namespace App\Filters;

use Illuminate\Http\Request;

class QueryFilter
{
    protected $request, $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($query)
    {
        $this->builder = $query;

        foreach ($this->filters() as $filterName => $filterValue) {
            if (method_exists($this, $filterName)) {
                $this->$filterName($filterValue);
            }
        }

        return $this->builder;
    }

    public function filters()
    {
        return json_decode($this->request->get('filters'));
    }
}
