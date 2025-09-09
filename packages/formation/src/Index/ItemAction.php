<?php

namespace Formation\Index;

use Closure;
use App\Actions\Formation\Index\Operation;

/**
 * @method $this rowClickable() Set row clickable for the action
 * @method $this iconClickable() Set icon clickable for the action
 * @method $this danger() Set the action to be shown as danger
 * @method $this break() Set the action to have a break line
 * @method $this custom(string $path) Specify the custom path for the action
 * @method $this itemType(string $itemType) Specify the item type for the action
 */

trait ItemAction
{
    public $items = [];

    public function __construct(Closure $callback = null)
    {
        if (! is_null($callback)) {
            $callback($this);
        }
    }

    public function operation($name): Operation
    {
        return $this->items[] = new Operation($name);
    }
}