<?php

namespace Formation\Index;

use Closure;
use App\Actions\Formation\Index\Label;

/**
 * @method $this badge(string $badge) Set badge for the tab
 * @method $this filter(array $filter) Set filter for the tab
 */

trait IndexTab
{
    public $items = [];

    public function __construct(Closure $callback = null)
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function label($name): Label
    {
        return $this->items[] = new Label($name);
    }
}