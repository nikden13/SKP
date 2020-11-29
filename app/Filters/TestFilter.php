<?php


namespace App\Filters;


class TestFilter extends QueryFilter
{
    protected function name($value)
    {
        $this->builder = $this->builder->where('name', $value);
    }

    protected function done($value)
    {
        $this->builder = $this->builder->where('done', $value);
    }

    protected function created_at($value)
    {
        $this->builder = $this->builder->where('created_at', $value);
    }

    protected function updated_at($value)
    {
        $this->builder = $this->builder->where('updated_at', $value);
    }
}
