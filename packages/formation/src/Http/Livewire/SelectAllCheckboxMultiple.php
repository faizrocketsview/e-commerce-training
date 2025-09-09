<?php

namespace App\Http\Livewire;

use App\Actions\Formation\Form\Option;

use Livewire\Component;

class SelectAllCheckboxMultiple extends Component
{
   /** @var array<\App\Actions\Formation\Form\Option> $items */
    public $items = [];

    /** @var array<string> $items */
    public $selectedItems = [];

    /** @var array<string> $originalSelectedItems */
    public $originalSelectedItems = [];

    /** @var array<string> $parentItems 
     * useful for debugging when lazy loading is enabled
    */
    public $parentItems = [];

    public string $name;

    public string $source;

    public function render()
    {
        return view('components.input.select-all-checkbox-multiple');
    }

    public function mount(array $items = []) 
    {               
        if(sizeof($items) === 0) {
            throw new \Exception("Items cannot be empty. Please provide at least one item.");
        }

        if(sizeof($items) > 30) {
            throw new \Exception("Items cannot exceed 30. Please provide a maximum of 30 items.");
        }

        $this->items = $items;
        $this->originalSelectedItems = $this->selectedItems;
    }

    public function updatedSelectedItems()
    {        
        $this->emitToParent();
    }

    public function selectAll()
    {
        $this->selectedItems = collect($this->items)->pluck('name')->toArray();

        $this->emitToParent();
    }

    public function deselectAll()
    {
        $this->selectedItems = [];

        $this->emitToParent();
    }

    public function toggleAll()
    {
        if (count($this->selectedItems) === count($this->items)) {
            $this->deselectAll();
        } else {
            $this->selectAll();
        }
    }

    public function emitToParent() {
        $old = $this->originalSelectedItems;
        $new = $this->selectedItems;
        $added = array_values(array_diff($new, $old));
        $removed = array_values(array_diff($old, $new));

        $this->emitUp('selectionChanged', 
            $this->name,
            $this->source,
            $old,
            $new,
            $added,
            $removed,
        );
    }
}
