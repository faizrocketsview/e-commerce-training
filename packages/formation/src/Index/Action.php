<?php

namespace Formation\Index;

use Closure;
use App\Actions\Formation\Index\Operation;

trait Action
{
    public $items = [];

    public function __construct(Closure $callback = null)
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function operation($name): Operation
    {
        return $this->items[] = new Operation($name);
    }
}