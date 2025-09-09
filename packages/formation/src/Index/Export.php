<?php

namespace Formation\Index;

use Closure;
use App\Actions\Formation\Index\Field;

trait Export
{
    public $items = [];

    public function __construct(Closure $callback = null)
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function field(string $name, string $label = null): Field
    {
        return $this->items[] = new Field($name, ($label ?? $name) ?? $name);
    }
}