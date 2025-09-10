<?php

namespace Formation\Http\Controllers;

use Livewire\Component;
use Formation\DataTable\WithDataTable;
use Spatie\Translatable\HasTranslations;


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
                            
                            if(isset($attributes->{$field->name})) {
                                $value = $attributes->{$field->name};
                                // If field is translatable and a plain string was sent, mirror to both en and ms
                                if ($this->isFieldTranslatable($field->name)) {
                                    if (is_string($value)) {
                                        $value = ['en' => $value, 'ms' => $value];
                                    } elseif (is_object($value)) {
                                        $value = (array) $value;
                                        // Fill missing locales with whichever exists
                                        if (!isset($value['en']) && isset($value['ms'])) $value['en'] = $value['ms'];
                                        if (!isset($value['ms']) && isset($value['en'])) $value['ms'] = $value['en'];
                                    }
                                }
                                $this->editing->{$field->name} = $value;
                            }
                        }
        
        try {
            $result['data']['attributes'] = $this->executeSave();
            return $result;
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'The given data was invalid.',
                'errors' => $this->formatValidationErrors($e->errors()),
            ], 422);
        }
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

                            if(isset($attributes->{$field->name})) {
                                $value = $attributes->{$field->name};
                                if ($this->isFieldTranslatable($field->name)) {
                                    if (is_string($value)) {
                                        $value = ['en' => $value, 'ms' => $value];
                                    } elseif (is_object($value)) {
                                        $value = (array) $value;
                                        if (!isset($value['en']) && isset($value['ms'])) $value['en'] = $value['ms'];
                                        if (!isset($value['ms']) && isset($value['en'])) $value['ms'] = $value['en'];
                                    }
                                }
                                $this->editing->{$field->name} = $value;
                            }
                        }
                        
        try {
            $result['data']['attributes'] = $this->executeSave();
            return $result;
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'The given data was invalid.',
                'errors' => $this->formatValidationErrors($e->errors()),
            ], 422);
        }
    }

    /**
     * Validation rules for API endpoints.
     * Force API to validate translatable fields on the root key (e.g., editing.name)
     * and delegate to the shared module rules generator to keep behavior consistent.
     */
    public function rules()
    {
        return $this->getModuleRules();
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

    private function isFieldTranslatable(string $fieldName): bool
    {
        try {
            $modelClass = $this->getModelProperty();
            if (!class_exists($modelClass)) return false;
            if (!in_array(HasTranslations::class, class_uses_recursive($modelClass))) return false;
            $model = app($modelClass);
            return method_exists($model, 'isTranslatableAttribute') && $model->isTranslatableAttribute($fieldName);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function formatValidationErrors(array $errors): array
    {
        // Transform flat keys like 'editing.name.en' into nested array under 'editing' key
        $grouped = [];
        foreach ($errors as $key => $messages) {
            $segments = explode('.', $key);
            // Ensure top-level 'editing' grouping as expected by API clients/tests
            if ($segments[0] !== 'editing') {
                // keep non-editing keys as-is
                $grouped[$key] = $messages;
                continue;
            }

            // Build nested structure under errors['editing'][...]
            $ref =& $grouped['editing'];
            for ($i = 1; $i < count($segments); $i++) {
                $segment = $segments[$i];
                if (!isset($ref[$segment])) {
                    $ref[$segment] = [];
                }
                $ref =& $ref[$segment];
            }

            // Assign messages (ensure array of strings)
            $ref = $messages;
            unset($ref); // break reference
        }

        return $grouped;
    }
}
