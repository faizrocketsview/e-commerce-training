<?php

namespace App\Http\Livewire\User;

use App\Http\Livewire\Resource as BaseResource;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class Resource extends BaseResource
{
    public function mount($module = 'users', $moduleGroup = 'managements', $moduleSection = 'ecommerce')
    {
        parent::mount($module, $moduleGroup, $moduleSection);
    }

    /**
     * Override rules method to add validation rules for permission fields
     */
    public function rules()
    {
        // Get rules from the WithDataTable trait
        $rules = $this->getModuleRules();
        
        // Add validation rules for permission fields
        $permissionFields = [
            'permissions_categories',
            'permissions_products', 
            'permissions_orders',
            'permissions_items',
            'permissions_users'
        ];
        
        foreach ($permissionFields as $field) {
            $rules['editing.' . $field] = ['nullable', 'array'];
            $rules['editing.' . $field . '.*'] = ['nullable', 'integer'];
        }
        
        return $rules;
    }

    /**
     * Process permission field data before validation and saving
     */
    public function updatedEditing($value, $key)
    {
        // Handle permission fields - ensure they are arrays
        $permissionFields = ['permissions_categories', 'permissions_products', 'permissions_orders', 'permissions_items', 'permissions_users'];
        if (in_array($key, $permissionFields)) {
            if (is_null($this->editing->$key) || empty($this->editing->$key)) {
                $this->editing->$key = [];
            } elseif (!is_array($this->editing->$key)) {
                // Convert string to array if needed
                if (is_string($this->editing->$key)) {
                    $this->editing->$key = explode('|', $this->editing->$key);
                } else {
                    $this->editing->$key = [];
                }
            }
        }
    }

    /**
     * Override executeSave to handle permission synchronization
     */
    public function executeSave()
    {
        if ($this->type === 'create')
            $this->authorize('create', [$this->model, $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);

        if ($this->type === 'edit')
            $this->authorize('update', [$this->editing, $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);

        // Process permission fields before saving
        $permissionFields = [
            'categories',
            'products', 
            'orders',
            'items',
            'users'
        ];

        // Store permission data temporarily (SaveAction will handle removal)
        $permissionData = [];
        foreach ($permissionFields as $field) {
            if (isset($this->editing->$field)) {
                $value = $this->editing->$field;
                if (is_string($value)) {
                    $value = explode('|', $value);
                }
                if ($value instanceof \Illuminate\Support\Collection) {
                    $value = $value->toArray();
                }
                $permissionData[$field] = array_filter($value);
            }
        }

        // Process other form fields (excluding permission fields)
        foreach ($this->form->items as $tab)
            foreach ($tab->items as $card)
                foreach ($card->items as $section)
                    foreach ($section->items as $column)
                        foreach ($column->items as $field)
                        {
                            $fieldName = $field->name;
                            
                            // Skip permission fields - they're handled separately
                            if (in_array($fieldName, $permissionFields)) {
                                continue;
                            }
                            
                            if ($field->type === "checkboxMultiple" || $field->type === "checkboxButtonMultiple")
                            {
                                if (isset($this->editing->$fieldName))
                                {
                                    $value = $this->editing->$fieldName;
                                    if (is_string($value)) {
                                        $value = explode('|', $value);
                                    }
                                    if ($value instanceof \Illuminate\Support\Collection) {
                                        $value = $value->toArray();
                                    }
                                    $this->editing->$fieldName = array_filter($value);
                                }
                            }

                            if ($field->type === 'file')
                            {
                                $this->editing->$fieldName = $this->unserializeFiles($this->editing->$fieldName, $fieldName);
                                $this->serializeFiles[$fieldName] = $this->serializeFiles($fieldName);                                    
                            }
                        }

        $this->validate();
        $subClasses = [];

        // Process other form fields (excluding permission fields)
        foreach ($this->form->items as $tab)
            foreach ($tab->items as $card)
                foreach ($card->items as $section)
                    foreach ($section->items as $column)
                        foreach ($column->items as $field)
                        {
                            $fieldName = $field->name;

                            // Skip permission fields - they're handled separately
                            if (in_array($fieldName, $permissionFields)) {
                                continue;
                            }

                            if ($field->type === "checkboxMultiple")
                            {
                                if (isset($this->editing->$fieldName))
                                    $temp = $this->editing->$fieldName;
                                    if ($temp instanceof \Illuminate\Support\Collection) {
                                        $temp = $temp->toArray();
                                    }
                                    ksort($temp);
                                    $this->editing->$fieldName = implode('|', array_filter($temp));
                            }

                            if ($field->type === "preset") 
                            {
                                $this->editing->$fieldName = $field->default;
                            }

                            if ($field->type === "subfieldBox") 
                            {
                                $subClasses[] = $field->with;

                                if (isset($this->subClassItems[$field->with]))
                                {
                                    foreach ($field->items as $subField) {
                                        if ($subField->type == 'preset') {
                                            foreach ($this->subClassItems[$field->with] as &$val) {
                                                $subFieldName = $subField->name;
                                                $val[$subFieldName] = $subField->default;
                                            }
                                        }
                                    }
                                }
                            }
                        }

        $defaultEditing = clone($this->editing);

        if(sizeof($this->fileColumnNames) > 0){
            foreach($this->fileColumnNames as $key => $fileColumnName) {
                if(isset($this->editing->{$fileColumnName})){
                    unset($this->editing->{$fileColumnName});
                }
            }
        }
                 
        $className = 'App\\Actions\\'.class_basename($this->model).'\\'.str_replace(' ', '', ucwords(str_replace('-', ' ', Str::singular($this->moduleSection)))).str_replace(' ', '', ucwords(str_replace('-', ' ', Str::singular($this->moduleGroup)))).str_replace(' ', '', ucwords(str_replace('-', ' ', Str::singular($this->module)))).'SaveAction';
        if (!class_exists($className))
        {
            $className = 'Formation\\Actions\\SaveAction';
        }

        $parentWithoutRelations = clone($this->editing);

        // Ensure permission fields are present on the object for SaveAction to sync
        foreach ($permissionFields as $field) {
            $parentWithoutRelations->$field = $permissionData[$field] ?? [];
        }

        foreach ($subClasses as $subClass) {
            unset($parentWithoutRelations->$subClass);
        }

        $this->editing->id = (new $className)->execute($parentWithoutRelations, $this->type); 
        if(sizeof($this->fileColumnNames) > 0){
            $this->saveAttachment($defaultEditing);
        }
        $this->executeSaveSubClass($subClasses);

        return $this->editing;
    }


    /**
     * Override executeEdit to load user permissions
     */
    public function executeEdit()
    {
        // Load the user without permission fields first
        $this->editing = $this->model::findOrFail($this->formId);
        
        // Load permissions separately
        $this->loadUserPermissions();
        
        // Set authorization
        $this->authorize('update', [$this->editing, $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);
        
        $this->useCachedRows();
    }

    /**
     * Load user permissions for editing
     */
    protected function loadUserPermissions()
    {
        // Get the actual user with full permissions (direct + via roles)
        $user = \App\Models\User::find($this->editing->id);
        if (!$user) {
            return;
        }

        $userPermissions = $user->getAllPermissions()->pluck('id')->toArray();

        // Group permissions by module (field names are now permissions_categories, permissions_products, etc.)
        $permissionFields = [
            'permissions_categories' => 'categories',
            'permissions_products' => 'products', 
            'permissions_orders' => 'orders',
            'permissions_items' => 'items',
            'permissions_users' => 'users'
        ];

        foreach ($permissionFields as $fieldName => $moduleName) {
            $modulePermissions = Permission::where('name', 'like', "ecommerce.managements.{$moduleName}:%")
                ->pluck('id')
                ->toArray();
            
            $userModulePermissions = array_intersect($userPermissions, $modulePermissions);
            $this->editing->$fieldName = array_values($userModulePermissions);
        }
    }

    /**
     * Override executeCreate to initialize permission fields
     */
    public function executeCreate()
    {
        parent::executeCreate();
        
        // Initialize permission fields as empty arrays
        $permissionFields = [
            'permissions_categories',
            'permissions_products', 
            'permissions_orders',
            'permissions_items',
            'permissions_users'
        ];

        foreach ($permissionFields as $field) {
            $this->editing->$field = [];
        }
    }

    /**
     * Check if model is translatable for a specific column
     */
    private function isModelTranslatable($model, string $column): bool 
    {
        return in_array(\Spatie\Translatable\HasTranslations::class, class_uses_recursive($model::class)) && $model->isTranslatableAttribute($column);
    }

    /**
     * Handle file unserialization
     */
    public function unserializeFiles($fileCategories, $fieldName) {
        $tempFiles = null;

        if(is_string($fileCategories)) { // data exist but no changes made
            $tempFiles = $fileCategories;
        }
        elseif(is_array($fileCategories)) { // changes has been made
            if(sizeof($fileCategories)) {
                $tempFiles = [];

                foreach($fileCategories as $category => $files) {
                    if($category === 'temporary') {
                        foreach($files as $file) {
                            $tempFiles[$file['position']] = \Livewire\TemporaryUploadedFile::unserializeFromLivewireRequest($file)['serializedFile'];
                        }
                    } elseif($category === 'existing') {
                        foreach($files as $file) {
                            $tempFiles[$file['position']] = $file['path'];
                        }
                    } else {
                        if(sizeof($this->serializeFiles) > 0 ){
                            if(isset($this->serializeFiles[$fieldName])) {
                                if(\Livewire\TemporaryUploadedFile::canUnserialize($this->serializeFiles[$fieldName])) {
                                    $tempFiles[] = \Livewire\TemporaryUploadedFile::unserializeFromLivewireRequest($this->serializeFiles[$fieldName]);
                                    
                                }
                            }
                        }
                    }
                }

                $tempFiles = $tempFiles[0];
            }
        }
        return $tempFiles;
    }

    /**
     * Handle file serialization
     */
    public function serializeFiles($fieldName){
        if(is_string($this->editing->$fieldName)) {
            return $this->editing->$fieldName;
        }else {
            if($this->editing->$fieldName instanceof \Livewire\TemporaryUploadedFile) {
                return $this->editing->$fieldName->serializeForLivewireResponse();
            }
        }
    }

    /**
     * Save attachment files
     */
    private function saveAttachment($defaultEditing){
        $object = $this->model::find($this->editing->id);

        foreach($this->fileColumnNames as $fileColumnName){
            $file = $defaultEditing->{$fileColumnName};
            if($file instanceof \Livewire\TemporaryUploadedFile) {
                $key = 0;
                $folderPath = $this->folderPaths[$fileColumnName];
                $extension = $file->extension();
                $file->getClientOriginalName();
                $fileName =  $fileColumnName . "_" . $this->editing->id. "_" . $key . "_" . date('YmdHis') . "." . $extension;
                $file->storePubliclyAs($folderPath, $fileName, 's3');

                $object->{$fileColumnName} = $fileName;
            }
        }

        $object->save();
    }

    /**
     * Execute save for subclasses
     */
    public function executeSaveSubClass($subClasses)
    {
        foreach($subClasses as $subClass)
        {
            $className = 'App\\Actions\\'.ucwords(Str::singular($subClass)).'\\'.str_replace(' ', '', ucwords(str_replace('-', ' ', Str::singular($this->moduleSection)))).str_replace(' ', '', ucwords(str_replace('-', ' ', Str::singular($this->moduleGroup)))).ucwords(Str::singular($subClass));
            $saveActionClassName = $className . 'SaveAction';
            if (!class_exists($saveActionClassName)) {
                $saveActionClassName = 'Formation\\Actions\\SaveAction';
            }

            if ($this->type == 'edit') {
                if (isset($this->editedSubClassFields[$subClass]) && count($this->editing->$subClass) != count($this->editedSubClassFields[$subClass])) 
                {
                    $existingIds = collect($this->editedSubClassFields[$subClass])->pluck('id')->toArray();
                    $currentRecords = $this->model::find($this->editing->id)->$subClass->all();

                    foreach($currentRecords as $record) {
                        if (!in_array($record->id, $existingIds)) {
                            $destroyActionClassName = $className . 'DestroyAction';

                            if (!class_exists($destroyActionClassName))
                            {
                                $destroyActionClassName = 'Formation\\Actions\\DestroyAction';
                            }

                            (new $destroyActionClassName)->execute($record);                                                
                        }
                    }                    
                }

                foreach($this->editing->$subClass as $object) 
                {
                    if ($object->isDirty())
                    (new $saveActionClassName)->execute($object, $this->type);
                }
            }

            if (isset($this->subClassItems[$subClass])) {

                $saveActionClassName = $className . 'SaveAction';
                
                foreach ($this->subClassItems[$subClass] as &$item) {

                    $subClassObject = $this->convertArrayDataToObject($this->editing, ucwords(Str::singular($subClass)), $item, $this->type);

                    if (!class_exists($saveActionClassName)) {
                        $saveActionClassName = 'Formation\\Actions\\SaveAction';
                    }

                    $item['id'] = (new $saveActionClassName)->execute($subClassObject, $this->type);
                }
            }

            $this->editing->$subClass = $this->model::find($this->editing->id)->$subClass;
        }
    }

    /**
     * Convert array data to object
     */
    public function convertArrayDataToObject(Object $parent, String $model, array $items, String $actionType)
    {
        $model = "App\Models\\".$model;
        $object = app($model);
        
        foreach ($items as $key => $value) {
            $object->$key = $value;
        }

        if ($actionType == 'create') {
            $parentClassForeignIdColumnName = Str::snake(class_basename(get_class($parent)))."_id";
            $object->{$parentClassForeignIdColumnName} = $parent->id;
        }

        return $object;
    }
}
