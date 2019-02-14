<?php namespace App\Support\Email;

use Illuminate\Support\Collection;

class ViewData extends Collection
{
    public function add(array $values)
    {
        foreach ($values as $k => $v) {
            $this->put($k, $v);
        }
    }
}