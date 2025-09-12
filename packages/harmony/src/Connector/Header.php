<?php

namespace Harmony\Connector;

use Closure;

class Header
{
    public $headers = [];

    public function __construct(Closure $callback = null)
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function type($key, $value): string
    {
        return $this->headers[$key] = $value;
    }
}