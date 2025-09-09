<?php

namespace Formation\DataTable;

use Illuminate\Support\Str;

trait WithFiltering
{
    public $showFilter = false;
    public $filters = [];

    public function renderingWithFiltering()
    {
        foreach ($this->filters as $field => $value)
        {
            if (!isset($value))
            {
                $this->filters[$field] = '';
            }
        }
    }

    public function toggleShowFilter()
    {
        //$this->useCachedRows();

        $this->showFilter = ! $this->showFilter;
    }

    public function resetFilter()
    {
        
        foreach ($this->index->filter->items as $indexFilter) 
        {
            if ($indexFilter->type != 'hidden')
                if($indexFilter->type == 'checkboxMultiple') {
                    $this->filters = Arr::dot($this->filters);
                    foreach($indexFilter->options as $option){
                        unset($this->filters[($indexFilter->with ? $indexFilter->with.'-'.$indexFilter->reference : $indexFilter->label). '.' . $option->name]);
                    }
                    $this->filters = Arr::undot($this->filters);
                }else {
                    unset($this->filters[($indexFilter->with ? $indexFilter->with.'-'.$indexFilter->reference : $indexFilter->label)]);

                }
        }


        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
        $this->selectPage = false;
    }

    public function updatedFilters()
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
        $this->selectPage = false;
    }

    public function applyFiltering($query)
    {
        $className = 'App\\Actions\\' . class_basename($this->model) . '\\' . str_replace(" ", "", ucwords(str_replace("-", " ", Str::singular($this->moduleGroup)))) . str_replace(" ", "", ucwords(str_replace("-", " ", Str::singular($this->module)))) . 'FilterAction';
        
        if (!class_exists($className)) {
            $className = 'Formation\\Actions\\FilterAction';
        }

        $query = (new $className)->execute($query, $this);

        return $query;
    }
}