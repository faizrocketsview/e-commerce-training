<?php

namespace Formation\Index;

use Closure;
use App\Actions\Formation\Form\Field;

trait Filter
{
    public $items = [];

    public function __construct(Closure $callback = null)
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function text(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function number(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function password(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function passwordConfirmation(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function email(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function phoneNumber(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function url(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function date(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function time(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function month(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function week(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function color(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function range(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function hidden(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function textarea(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function richText(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function select(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function radio(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function radioButton(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function checkbox(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function checkboxButton(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function checkboxMultiple(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function checkboxButtonMultiple(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function file(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function image(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function coordinate(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }

    public function preset(String $name, String $label = null): Field
    { 
        return $this->items[] = new Field($name, $label, __FUNCTION__);
    }
}