<?php

namespace Formation\DataTable;

trait WithSorting 
{
    public $sorts = [];

    public function sortByDefault()
    {
        if (empty($this->sorts))
        {
            foreach ($this->index->select->items as $item)
                if (isset($item->sortByDefault))
                {
                    $this->sorts[$item->name] = $item->sortByDefault;
                }
        }
    }
    
    public function sortBy($field)
    {
        if (empty($this->sorts[$field])) return $this->sorts[$field] = 'asc';

        if ($this->sorts[$field] === 'asc') return $this->sorts[$field] = 'desc';

        if ($this->sorts[$field] === 'desc') return $this->sorts[$field] = '';
    }

    public function applySorting($query)
    {
        return tap($query, function($query) {

            if ($this->type == 'reorder')
            {
                $sorts['order'] = 'asc';
            }
            else {
                $sorts = $this->sorts;
            }

            foreach ($sorts as $field => $direction) 
            {
                if (!empty($direction))
                {
                    $query->orderBy($field, $direction);
                }
            }
        });
    }
}