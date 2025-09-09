<?php

namespace Formation\DataTable;

trait WithItemsSelection
{
    public $selectPage = false;
    public $selectAll = false;
    public $selected = [];

    public function renderingWithItemsSelection()
    {
        if ($this->selectAll)
            $this->selected = $this->pageItems;
    }

    public function updatedSelected()
    {
        $this->selectAll = false;
        $this->selectPage = false;
    }

    public function updatedSelectPage($value)
    {
        if ($this->selectAll) {
            $this->selected = [];
            $this->selectAll = false;
        }
        else {
            if ($value) {
                $this->selected = collect($this->selected)->merge($this->pageItems)->unique()->values();
            }
            else {
                $this->selected = collect($this->selected)->diff($this->pageItems)->values();
            }
        }
    }

    public function getPageItemsProperty()
    {
        return $this->items->pluck('id')->map(fn($id) => (string) $id);
    }

    public function selectAll()
    {
        $this->selectAll = true;
    }

    public function getSelectedItemsQueryProperty()
    {
        return (clone $this->itemsQuery)->unless($this->selectAll, fn($query) => $query->whereKey($this->selected));
    }
}