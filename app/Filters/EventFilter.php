<?php


namespace App\Filters;


class EventFilter extends QueryFilter
{

    protected function name($value)
    {
        $this->builder = $this->builder->where('name', $value);
    }

    protected function type_event($value)
    {
        $this->builder = $this->builder->where('type_event', $value);
    }

    protected function location($value)
    {
        $this->builder = $this->builder->where('location', $value);
    }

    protected function date($value)
    {
        $value = str_replace('/', '-', $value); //можно убрать
        $this->builder = $this->builder->where('date', $value);
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
