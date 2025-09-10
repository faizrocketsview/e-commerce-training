<?php

namespace App\Http\Livewire;

use Formation\DataTable\WithDataTable;
use Livewire\Component;


class Resource extends Component
{
    use WithDataTable;

    protected $listeners = ['updateFolderPath', 'updateFileColumnName', 'setFiles', 'previewImage', 'selectionChanged'];
    
    protected $queryString = ['itemId', 'itemType', 'sorts', 'search', 'filters', 'showFilter', 'perPage', 'type', 'formId', 'tab'];

    public $view = [
        'index' => 'formation.index',
        'form' => 'formation.form',
        'reorder' => 'formation.reorder',
    ];

    /**
     * Override rules method to add missing validation rules for permission fields
     */
    public function rules()
    {
        // Get rules from the WithDataTable trait
        $rules = $this->getModuleRules();
        
        // Add validation rules for permission fields only for Users module
        if ($this->module === 'users') {
            $permissionFields = [
                'permissions_categories',
                'permissions_products', 
                'permissions_orders',
                'permissions_items',
                'permissions_users'
            ];
            
            foreach ($permissionFields as $field) {
                $rules['editing.' . $field] = ['nullable', 'array'];
                $rules['editing.' . $field . '.*'] = ['nullable', 'array'];
            }
        }
        
        // Ensure translated fields have nested rules (e.g., editing.name.en)
        try {
            $modelClass = $this->getModelProperty();
            $modelInstance = app($modelClass);
            foreach ($this->getFormProperty()->items as $tab) {
                foreach ($tab->items as $card) {
                    foreach ($card->items as $section) {
                        foreach ($section->items as $column) {
                            foreach ($column->items as $field) {
                                if ($field->type === 'preset') continue;
                                if (method_exists($modelInstance, 'isTranslatableAttribute') &&
                                    in_array(\Spatie\Translatable\HasTranslations::class, class_uses_recursive($modelClass)) &&
                                    $modelInstance->isTranslatableAttribute($field->name)) {
                                    $lang = $field->lang ?? (\Illuminate\Support\Facades\App::getFallbackLocale() ?: 'en');
                                    $rules['editing.'.$field->name.'.'.$lang] = $field->rules;
                                    // Also accept plain string (API convenience): treat it as valid
                                    $rules['editing.'.$field->name] = ['nullable'];
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // silent; fall back to existing rules
        }
        
        return $rules;
    }

    /**
     * Process permission field data before validation
     */
    public function updatedEditing($value, $key)
    {
        // Handle permission fields only for Users module - ensure they are arrays
        if ($this->module === 'users' && strpos($key, 'permissions_') === 0) {
            if (is_null($this->editing->$key) || empty($this->editing->$key)) {
                $this->editing->$key = [];
            } elseif (!is_array($this->editing->$key)) {
                $this->editing->$key = [];
            }
        }
    }
}
