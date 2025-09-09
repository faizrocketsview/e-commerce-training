<?php

namespace Formation\Form;

use Closure;
use App\Actions\Formation\Form\Card;

trait Tab
{
    public $items = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function create(string $name): Card
    { 
        return $this->items[] = new Card($name);
    }

    public function group(object $object, int &$tabCount, Closure $callback): void
    {
        $tabCount++;
        
        if ($object->tab == $tabCount)
        {
            if (! is_null($callback)) {
                $callback($this);
            }
        }
    }
}