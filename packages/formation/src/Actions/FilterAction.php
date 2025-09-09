<?php

namespace Formation\Actions;

use Illuminate\Support\Arr;

class FilterAction
{
    public function execute($query, $object)
    {
        if (isset($object->index->filter->items))
        {
            $object->filters = Arr::undot(Arr::dot($object->filters));

            $this->convertFiltersToDottedArray($object);

            foreach($object->index->filter->items as $filter) {
                $filterHasNull = false;
                $objectFilters = array();
                $objectFilters = $object->filters;
                
                $filterName = ($filter->with ? $filter->with.'-'.$filter->reference : $filter->label);

                if (array_key_exists($filterName, $object->filters)) {

                    if ($object->filters[$filterName]) {

                        if ($object->type == 'reorder' && $filter->type != 'hidden')
                            continue;

                        if ($filter->with) {
                            if ($filter->operator === 'in') {
                                foreach ($object->filters[$filterName] as $key => $value) {
                                    if (!in_array($value, data_get($filter->options, '*.name')) || !in_array($key, data_get($filter->options, '*.name'))) {
                                        unset($object->filters[$filterName][$key]);
                                    }

                                    if(isset($objectFilters[$filterName][$key])){
                                        if($objectFilters[$filterName][$key] == false){
                                            unset($objectFilters[$filterName][$key]);
                                        }
                                    }
                                }

                                if(isset($objectFilters[$filterName]['(blank)'])){ 
                                    if($objectFilters[$filterName]['(blank)'] == '(blank)') {
                                        $objectFilters[$filterName]['(blank)'] = '';
                                    }
                                }

                                if(isset($objectFilters[$filterName]['(null)'])){ 
                                    if($objectFilters[$filterName]['(null)'] == '(null)') {
                                        $filterHasNull = true;
                                    }
                                }
                                
                                if (!empty($object->filters[$filterName])) {
                                    if($filterHasNull){
                                        $query->whereHas($filter->with, function ($query) use ($objectFilters, $filter, $filterName) {
                                            $query->where(function ($query) use ($filter, $filterName, $objectFilters){
                                                $query->whereIn($filter->reference, $objectFilters[$filterName])
                                                    ->orWhereNull($filter->reference);
                                            });
                                        });
                                    }else{
                                        $query->whereHas($filter->with, function ($query) use ($objectFilters, $filter, $filterName) {
                                            $query->whereIn($filter->reference, $objectFilters[$filterName]);
                                        });
                                    }
                                }
                            } else {
                                $query->whereHas($filter->with, function ($query) use ($object, $filter, $filterName) {
                                    if($object->filters[$filterName] === '(null)' || $object->filters[$filterName] === '(Null)' ) {
                                        $query->whereNull($filter->reference);
                                    } elseif($object->filters[$filterName] === '(blanks)' || $object->filters[$filterName] === '(Blanks)') {
                                        $query->where($filter->reference, '');
                                    } else {
                                        $query->where($filter->reference, $filter->operator, ($filter->operator === 'like') ? '%'.$object->filters[$filterName].'%' : $object->filters[$filterName]);
                                    }                                
                                });
                            }
                        }
                        else {
                            if ($filter->type === 'date')
                            {
                                $query->whereDate($filter->name, $filter->operator, ($filter->operator === 'like') ? '%'.$object->filters[$filterName].'%' : $object->filters[$filterName]);
                            }
                            else {
                                if ($filter->operator === 'in') {
                                    foreach ($object->filters[$filterName] as $key => $value) {
                        
                                        if (!in_array($value, data_get($filter->options, '*.name')) || !in_array($key, data_get($filter->options, '*.name'))) {
                                            unset($object->filters[$filterName][$key]);
                                        }

                                        if(isset($objectFilters[$filterName][$key])){
                                            if($objectFilters[$filterName][$key] == false){
                                                unset($objectFilters[$filterName][$key]);
                                            }
                                        }
                                    }
                                    
                                    if(isset($objectFilters[$filterName]['(blank)'])){ 
                                        if($objectFilters[$filterName]['(blank)'] == '(blank)') {
                                            $objectFilters[$filterName]['(blank)'] = '';
                                        }
                                    }

                                    if(isset($objectFilters[$filterName]['(null)'])){ 
                                        if($objectFilters[$filterName]['(null)'] == '(null)') {
                                            $filterHasNull = true;
                                        }
                                    }
                                    
                                    if (!empty($object->filters[$filterName])) {
                                        if($filterHasNull){
                                            $query->where(function ($query) use ($filter, $filterName, $objectFilters){
                                                $query->whereIn($filter->name, $objectFilters[$filterName])
                                                    ->orWhereNull($filter->name);
                                            });
                                        }else{
                                            $query->whereIn($filter->name, $objectFilters[$filterName]);
                                        }
                                    }
                                } elseif($filter->operator === 'json') {
                                    $query->whereRaw("JSON_SEARCH(lower($filter->name), 'all', ?) IS NOT NULL", ['%'.strtolower($object->filters[$filterName]).'%']);
                                } else {
                                    if($object->filters[$filterName] === '(null)' || $object->filters[$filterName] === '(Null)' ) {
                                        $query->whereNull($filter->name);
                                    } elseif($object->filters[$filterName] === '(blank)' || $object->filters[$filterName] === '(Blank)') {
                                        $query->where($filter->name, '');
                                    } else {
                                        $query->where($filter->name, $filter->operator, ($filter->operator === 'like') ? '%'.$object->filters[$filterName].'%' : $object->filters[$filterName]);
                                    }                                
                                }
                            }
                        }
                    }
                }
            }

            $object->filters = Arr::dot($object->filters);
        }

        return $query;
    }

    public function convertFiltersToDottedArray($object)
    {
        $dottedFilters = [];
        foreach ($object->filters as $key => $value) {
            $dottedFilters = array_merge($dottedFilters, $this->dotArray([$key => $value], ''));
        }

        $object->filters = $dottedFilters;
    }

    public function dotArray($array, $prefix) {
        $dotted = [];

        foreach ($array as $key => $value) {
            $currentKey = $prefix === '' ? $key : "$prefix.$key";

            if (is_array($value)) {
                if ($this->isAssociativeArray($value)) {
                    if (strpos($key, '-') !== false) {
                        $dotted[$currentKey] = $value;
                    } elseif ($this->containsOnlySimpleValues($value)) {
                        foreach ($value as $innerKey => $innerValue) {
                            if (strpos($innerKey, '-') !== false) {
                                $dotted[$currentKey.'.'.$innerKey] = $innerValue;
                            } else {
                                $dotted[$currentKey] = $value;
                            }
                        }
                    } else {
                        foreach ($this->dotArray($value, $currentKey) as $nestedKey => $nestedValue) {
                            $dotted[$nestedKey] = $nestedValue;
                        }
                    }
                } else {
                    $dotted[$currentKey] = $value;
                }
            } else {
                $dotted[$currentKey] = $value;
            }
        }

        return $dotted;
    }

    public function isAssociativeArray(array $array) {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    public function containsOnlySimpleValues(array $array) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                return false;
            }
        }
        return true;
    }
}
