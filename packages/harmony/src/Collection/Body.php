<?php

namespace Harmony\Collection;

use Closure;

class Body
{
    public $items = [];

    public function __construct(Closure $callback = null)
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }
    
    public function field(string $name)
    {
        $this->items[] = $name;
        
        return $this;
    }
  
    public function group(string $name, Closure $callback): self
    {
        $group = new self();
        $callback($group);
        $this->items[] = [$name => $group->items];

        return $this;
    }
}