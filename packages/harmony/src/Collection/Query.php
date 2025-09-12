<?php

namespace Harmony\Collection;

use Closure;

class Query
{
    public $items = [];

    public function __construct(Closure $callback = null)
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function field($name): string
    {
        return $this->items[] = $name;
    }
}