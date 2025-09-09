<?php

namespace Formation\Index;

use Closure;
use App\Actions\Formation\Index\Badge;

/**
 * @method $this sortable() Set sortable for the field
 * @method $this sortByDefault(string $direction) Set the field to be sorted by default
 * @method $this align(string $align) Specify the field to be aligned left or right
 * @method $this truncate() Set the field to be truncated if cannot fit into the column
 * @method $this wrap() Set the field to be wrapped if too long
 * @method $this maxWidth() Set width of the field to be maximum
 * @method $this highlight() Set the field to be highlighted
 * @method $this localize() Set the field to be localized for language
 * @method $this display(string $display) Specify the minimum screen size of displaying the field
 * @method $this with(string $with) Specify the method of related model
 * @method $this reference(string $reference) Specify the reference column of the related model
 */

trait Field
{
    public $badges = [];

    public function __construct(string $name, string $label = null, string $type = null)
    {
        $this->name = $name;
        $this->label = $label ?? $name;

        if (isset($type))
            $this->type = $type;
    }

    public function group(Closure $callback): Void
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function badge(string $field, string $color = 'gray'): Void
    {
        $this->badges[$field] = new Badge($color);
    }
}