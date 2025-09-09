<?php

namespace Formation\Form;

use Closure;
use App\Actions\Formation\Form\Column;

trait Section
{
    public $items = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function create(String $name): Column
    { 
        return $this->items[] = new Column($name);
    }

    public function group(Closure $callback): void
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }
}