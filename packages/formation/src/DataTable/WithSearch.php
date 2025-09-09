<?php

namespace Formation\DataTable;

trait WithSearch
{
    public $search = '';

    public function applySearch($query)
    {
        return isset($this->index->search->items) ? $query->where(function ($query) {
            foreach ($this->index->search->items as $search)
            {
                if ($search->with)
                {
                    $query->orWhereHas($search->with, function ($query) use ($search) {
                        $query->where($search->reference, 'like', '%'.$this->search.'%');
                    });
                }
                elseif ($search->json) {
                    $query->orWhereRaw("JSON_SEARCH(lower($search->name), 'all', ?) IS NOT NULL", ['%'.strtolower($this->search).'%']);
                } else {
                    $query->orWhere($search->name, 'like', '%'.$this->search.'%');
                }
            }
        })
        : $query;
    }
}