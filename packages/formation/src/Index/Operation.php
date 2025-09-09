<?php

namespace Formation\Index;

use Closure;

trait Operation
{
    public $rules = [];
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function group(Closure $callback): Void
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function rules(Array $rules): Void
    {
        $this->required = in_array('required', $rules) ? true : null;

        $this->rules = $rules;
    }
}