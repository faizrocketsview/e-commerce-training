<?php

namespace Harmony;

use Harmony\Connector\Authorization;
use Harmony\Connector\Header;
use Closure;

class Connector
{
    public $authorization;
    public $header;

    public function __construct(Closure $callback = null)
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function authorization(Closure $callback):  Connector
    {
        $this->authorization = new Authorization($callback);

        return $this;
    }

    public function header(Closure $callback):  Connector
    {
        $this->header = new Header($callback);

        return $this;
    }
}