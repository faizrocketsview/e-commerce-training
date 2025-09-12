<?php

namespace Harmony\Collection;

use Closure;

class Request
{
    public $endpoint;
    public $method;
    public $bodyType;
    public $query;
    public $body;

    public function __construct(Closure $callback = null)
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function endpoint($url): string
    {
        return $this->endpoint = $url;
    }

    public function method($name): string
    {
        return $this->method = $name;
    }

    public function bodyType($name): string
    {
        return $this->bodyType = $name;
    }

    public function query(Closure $callback): Request
    {
        $this->query = new Query($callback);

        return $this;
    }

    public function body(Closure $callback): Request
    {
        $this->body = new Body($callback);

        return $this;
    }
}