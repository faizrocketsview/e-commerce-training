<?php

namespace Formation\Http\Controllers;

use Livewire\Component;
use Formation\DataTable\WithDataTable;


class ApiController extends Component
{
    use WithDataTable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($moduleSection, $moduleGroup, $module)
    {
        $this->moduleSection = $moduleSection;
        $this->moduleGroup = $moduleGroup;
        $this->module = $module;

        foreach (request()->all() as $key => $value)
        {
            if (in_array($key, ['itemId', 'sorts', 'search', 'filters', 'showFilter', 'perPage']))
                $this->$key = $value;
        }

        $this->executeIndex();

        return $this->items;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($moduleSection, $moduleGroup, $module)
    {
        $this->moduleSection = $moduleSection;
        $this->moduleGroup = $moduleGroup;
        $this->module = $module;
        $this->tab = $this->tab ?? 1;

        foreach (request()->all() as $key => $value)
        {
            if (in_array($key, ['itemId', 'sorts', 'search', 'filters', 'showFilter', 'perPage']))
                $this->$key = $value;
        }

        $this->type = 'create';
        $this->editing = $this->model::make();
        $attributes = json_decode(request()->getContent())->data->attributes;

        foreach ($this->form->items as $tab)
            foreach ($tab->items as $card)
                foreach ($card->items as $section)
                    foreach ($section->items as $column)
                        foreach ($column->items as $field)
                        {
                            if ($field->type === 'preset')
                                continue;
                            
                            if(isset($attributes->{$field->name}))
                                $this->editing->{$field->name} = $attributes->{$field->name};
                        }
        
        $result['data']['attributes'] = $this->executeSave();

        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($moduleSection, $moduleGroup, $module, $id)
    {
        $this->moduleSection = $moduleSection;
        $this->moduleGroup = $moduleGroup;
        $this->module = $module;
        $this->formId = $id;
        $this->tab = $this->tab ?? 1;

        foreach (request()->all() as $key => $value)
        {
            if (in_array($key, ['itemId', 'sorts', 'search', 'filters', 'showFilter', 'perPage']))
                $this->$key = $value;
        }

        $this->type = 'show';
        $this->executeShow();
        
        $result['data']['attributes'] = $this->editing;

        return $result;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($moduleSection, $moduleGroup, $module, $id)
    {
        $this->moduleSection = $moduleSection;
        $this->moduleGroup = $moduleGroup;
        $this->module = $module;
        $this->formId = $id;
        $this->tab = $this->tab ?? 1;

        foreach (request()->all() as $key => $value)
        {
            if (in_array($key, ['itemId', 'sorts', 'search', 'filters', 'showFilter', 'perPage']))
                $this->$key = $value;
        }

        $this->type = 'edit';
        $this->editing = $this->model::find($id);
        $attributes = json_decode(request()->getContent())->data->attributes;

        foreach ($this->form->items as $tab)
            foreach ($tab->items as $card)
                foreach ($card->items as $section)
                    foreach ($section->items as $column)
                        foreach ($column->items as $field)
                        {
                            if ($field->type === 'preset')
                                continue;

                            if(isset($attributes->{$field->name}))
                                $this->editing->{$field->name} = $attributes->{$field->name};
                        }
                        
        $result['data']['attributes'] = $this->executeSave();

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($moduleSection, $moduleGroup, $module, $id)
    {
        $this->moduleSection = $moduleSection;
        $this->moduleGroup = $moduleGroup;
        $this->module = $module;

        $this->item_to_be_deleted = $id;
        return $this->executeDestroy();
    }
}
