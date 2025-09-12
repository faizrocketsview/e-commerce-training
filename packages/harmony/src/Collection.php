<?php

namespace Harmony;

use Closure;
use Harmony\Collection\Request;

class Collection
{
    public $request;

    public function __construct(Closure $callback = null)
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function group(Closure $callback): Collection
    {
        $this->request = new Request($callback);

        return $this;
    }
}