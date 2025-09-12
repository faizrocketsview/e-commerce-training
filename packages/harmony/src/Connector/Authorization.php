<?php

namespace Harmony\Connector;

use Closure;

class Authorization
{
    public $type;

    public function __construct(Closure $callback = null)
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function type($name): string
    {
        return $this->type = $name;
    }
}