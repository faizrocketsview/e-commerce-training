<?php

namespace Formation\Form;

use Closure;
use App\Actions\Formation\Form\Section;

trait Card
{
    public $items = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function create(String $name): Section
    { 
        return $this->items[] = new Section($name);
    }

    public function group(Closure $callback): void
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }
}