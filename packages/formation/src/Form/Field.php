<?php

namespace Formation\Form;

use Closure;
use App\Actions\Formation\Form\Option;

/**
 * @method $this span(int $column) Specify how many columns to be spanned through by the field
 * @method $this autofocus() Set autofocus for the field
 * @method $this flex() Set the radio or checkbox options of the field to be flex
 * @method $this default(string $default) Specify the default value for the field
 * @method $this prepend(string $prepend) Specify the prepend value for the field
 * @method $this append(string $append) Specify the append value for the field
 * @method $this placeholder(string $placeholder) Specify the placeholder value for the field
 * @method $this readonly() Set readonly for the field
 * @method $this disabled() Set disabled for the field
 * @method $this lazy() Set lazy for the field
 * @method $this debounce(int $milliseconds) Specify the millisecounds debounce value for the field
 * @method $this href(string $href) Specify the href value for the field
 * @method $this copy() Set the field to be copyable to clipboard
 * @method $this with(string $with) Specify the with model relationship for the field
 * @method $this reference(string $reference) Specify field reference for the model relationship
 */

trait Field
{
    use \Formation\Form\Column;
    
    public $options = [];
    public $rules = [];

    public function __construct(string $name, string $label = null, string $type)
    {
        $this->name = $name;
        $this->label = $label ?? $name;
        $this->type = $type;
    }

    public function group(Closure $callback): Void
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function option(String $name, String $label = null): Void
    {
        $this->options[] = new Option($name, $label);
    }

    public function rules(Array $rules): Void
    {
        $this->required = in_array('required', $rules) ? true : null;

        $this->rules = $rules;
    }

    public function hide(Closure $callback)
    {
        if (! is_null($callback)) {
            $this->hide = $callback($this);
        }

        return $this;
    }
    
    public function value(Closure $callback)
    {
        if (! is_null($callback)) {
            $this->value = $callback($this);
        }

        return $this;
    }
}