<?php

namespace Formation\Form;

use Closure;
use App\Actions\Formation\Form\Tab;

/**
 * @method $this redirectView(string $view) Specify the view page to be redirected
 */

trait Form
{
    public $items = [];

    public function __construct(string $name, Closure $callback = null)
    {
        $this->name = $name;
        $this->redirectView = 'index';
        $this->navigateRecord = false;

        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function create(String $name): Tab
    { 
        return $this->items[] = new Tab($name);
    }
}