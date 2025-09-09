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
