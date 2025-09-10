<?php

namespace Formation\DataTable;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Formation\DataTable\WithFiltering;
use Formation\DataTable\WithSearch;
use Formation\DataTable\WithItemsSelection;
use Formation\DataTable\WithSorting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Livewire\WithPagination;
use Maatwebsite\Excel\HeadingRowImport;
use Livewire\TemporaryUploadedFile;
use Illuminate\Support\Facades\App;

trait WithDataTable
{
    use AuthorizesRequests;
    use WithPagination;
    use WithSorting;
    use WithItemsSelection;
    use WithFiltering;
    use WithSearch;
    use WithCache;
    
    public $module;
    public $moduleGroup;
    public $moduleSection;
    public $type = 'index';
    public $perPage = 20;
    public $showDeleteModal = false;
    public $showPreviewImageModal = false;
    public $previewImageUrl;
    public Object $editing;
    public $itemId;
    public $itemType;
    public $item_to_be_deleted;
    public $formId;
    public $tab = 1;
    public $editedSubClassFields;
    public $subClassItems;
    public $orderedList = [];
    protected $validationAttributes = [];
    public bool $isUploading = false;
    public $folderPaths = [];
    public $fileColumnNames = [];
    public array $serializeFiles = [];
    public $import;
    public $attachment;
    public $importColumns = [];
    public $columns = [];
    public $fieldColumnMap = [];
    public $importType = '';
    
    public function mount($module, $moduleGroup, $moduleSection)
    {
        $this->module = $module;
        $this->moduleGroup = $moduleGroup;
        $this->moduleSection = $moduleSection;
    }
    
    public function render()
    {
        $this->validateIndexTabFilters();
        
        if ($this->type === 'show' || $this->type === 'create' || $this->type === 'edit')
        {
            if (!isset($this->editing))
            {
                $this->{'execute'.ucwords($this->type)}();
            }

            $this->setFormValue();

            return view($this->view['form'], [
                'type' => $this->type,
                'form' => $this->form,
                'selectedTab' => $this->tab,
                'formId' => $this->formId,
                'module' => $this->module,
                'moduleGroup' => $this->moduleGroup,
                'moduleSection' => $this->moduleSection,
            ]);
        }
        else if ($this->type === 'reorder')
        {
            $this->executeReorder();

            return view($this->view['reorder'], [
                'items' => $this->items,
                'filters' => $this->filters,
                'indexSelectItems' => $this->index->select->items,
                'filterItems' => isset($this->index->filter->items)?$this->index->filter->items:null,
                'searchItems' => isset($this->index->search->items)?$this->index->search->items:null,
                'module' => $this->module,
                'moduleGroup' => $this->moduleGroup,
                'moduleSection' => $this->moduleSection,
            ]);
        }
        else
        {
            $this->executeIndex();

            if (count($this->getErrorBag()->all()))
            {
                $this->showDeleteModal = false;
                $this->notify('error', 'Failed to delete.', $this->getErrorBag()->first());
                $this->resetErrorBag();
            }

            return view($this->view['index'], [
                'items' => $this->items,
                'filters' => $this->filters,
                'poll' => isset($this->index->poll)?$this->index->poll:null,
                'paginateType' => isset($this->index->paginate)?$this->index->paginate:'standard',
                'indexSelectItems' => isset($this->index->select->items)?$this->index->select->items:[],
                'filterItems' => isset($this->index->filter->items)?$this->index->filter->items:[],
                'searchItems' => isset($this->index->search->items)?$this->index->search->items:[],
                'actionItems' => isset($this->index->action->items)?$this->index->action->items:[],
                'itemActionItems' => isset($this->index->itemAction->items)?$this->index->itemAction->items:[],
                'indexTabItems' => isset($this->index->indexTab->items)?$this->index->indexTab->items:[],
                'module' => $this->module,
                'moduleGroup' => $this->moduleGroup,
                'moduleSection' => $this->moduleSection,
            ]);
        }
    }

    public function cancel()
    {
        $this->type = 'index';
        $this->resetErrorBag();
    }

    public function executeIndex()
    {
        $this->formId = null;
        $this->tab = null;
        unset($this->editing);
        unset($this->editedSubClassFields);
        unset($this->subClassItems);    
        $this->authorize('viewAny', [$this->model, $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);
        $this->sortByDefault();
    }

    public function sortByDefault()
    {
        $this->useCachedRows();
    }

    public function useCachedRows()
    {
        // Initialize the index if it's not already set
        if (!isset($this->index)) {
            $this->index = $this->getIndexProperty();
        }
    }

    public function setFormValue()
    {
        foreach ($this->form->items as $tab)
            foreach ($tab->items as $card)
                foreach ($card->items as $section)
                    foreach ($section->items as $column)
                        foreach ($column->items as $field)
                        {
                            if ($field->type === "preset")
                                continue;

                            if (isset($field->value))
                            {
                                $fieldName = $field->name;
                                $this->editing->$fieldName = $field->value;
                            }
                        }
    }

    public function rules()
    {
        if ($this->type == "index") {
            foreach ($this->index->itemAction->items as $itemAction) {
                if ($itemAction->name == 'delete') {
                    $rules['item_to_be_deleted'] = $itemAction->rules;
                }
            }
        }
        else {
            foreach ($this->getFormProperty()->items as $tab)
                foreach ($tab->items as $card)
                    foreach ($card->items as $section)
                        foreach ($section->items as $column)
                        foreach ($column->items as $field)
                        {
                            if ($field->type === "preset")
                                continue;
                            
                            if ($field->type === "file") {
                                    if(is_string($this->editing->{$field->name})){
                                        $rules['editing.'.$field->name] = ['required'];
                                        $rules['editing.'.$field->name.'.*'] = ['required'];
                                    }else {
                                        $rules['editing.'.$field->name] = $field->rules;
                                        $rules['editing.'.$field->name.'.*'] = $field->rules;
                                    }
                                } elseif ($field->type === "checkboxMultiple" || $field->type === "checkboxButtonMultiple") {
                                    $rules['editing.'.$field->name.'.*'] = $field->rules;
                                    
                                    // Also add array rules if they exist
                                    if (isset($field->arrayRules)) {
                                        $rules['editing.'.$field->name.'.*'] = $field->arrayRules;
                                    }

                                } elseif ($field->type === "subfieldBox") {
                                    foreach ($field->items as $key => $subfield)
                                    {
                                        if ($subfield->type != "preset") {

                                            $fieldName = isset($field->with) ? $field->with : $field->name;

                                            if ($subfield->type === "checkboxMultiple") {
                                                $rules['editing.'.$fieldName.'.*.'.$subfield->name.'.*'] = $subfield->rules;
                                                $rules['subClassItems.'.$fieldName.'.*.'.$subfield->name.'.*'] = $subfield->rules;
                                                
                                                $this->validationAttributes['editing.'.$fieldName.'.*.'.$subfield->name.'.*'] = __($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$field->name) .' '. strtolower(__($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$subfield->name));
                                                $this->validationAttributes['subClassItems.'.$fieldName.'.*.'.$subfield->name.'.*'] = __($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$field->name) .' '. strtolower(__($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$subfield->name));
                                            }else {
                                                $rules['editing.'.$fieldName.'.*.'.$subfield->name] = $subfield->rules;
                                                $rules['subClassItems.'.$fieldName.'.*.'.$subfield->name] = $subfield->rules;
                                                
                                                $this->validationAttributes['editing.'.$fieldName.'.*.'.$subfield->name] = __($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$field->name) .' '. strtolower(__($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$subfield->name));
                                                $this->validationAttributes['subClassItems.'.$fieldName.'.*.'.$subfield->name] = __($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$field->name) .' '. strtolower(__($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$subfield->name));                 
                                            }                                            
                                        }
                                    }
                                } elseif ($this->isModelTranslatable($this->editing, $field->name)) {
                                    // For API requests, validate the root key; web validates specific locale
                                    $isApi = \Illuminate\Support\Facades\Request::is('api/*');
                                    if ($isApi) {
                                        $rules["editing.$field->name"] = $field->rules;
                                        // Make nested locale keys optional to avoid strict 'editing.name.en' requirement
                                        $rules["editing.$field->name.*"] = ['nullable'];
                                    } else {
                                        if(!isset($field->lang)) $field->lang = App::getFallbackLocale() ?: 'en';
                                        $rules["editing.$field->name.$field->lang"] = $field->rules;
                                    }
                                } else {
                                    $rules['editing.'.$field->name] = $field->rules;
                                }
                            }
        }

        return $rules;
    }

    public function getIndexProperty()
    {
        return $this->getClass()::index($this);
    }

    public function getFormProperty()
    {
        return $this->getClass()::form($this);
    }

    public function getClass()
    {
        if (Str::plural($this->module) === $this->module) 
        {
            $module =  Str::singular($this->module);
            $class = '\\App\\Formation\\'.Str::studly($this->moduleSection).'\\'.Str::studly($this->moduleGroup).'\\'.Str::studly($module).'Formation';

            if (class_exists($class))
            {
                return $class;
            }
            else
            {
                abort(404);
            } 
        }
        else
        {
            abort(404);
        }
    }

    public function validateIndexTabFilters()
    {
        if (isset($this->index) && isset($this->index->indexTab) && isset($this->index->indexTab->items)) {
            foreach($this->index->indexTab->items as $value)
            {
                foreach(array_keys($value['filter']) as $filter)
                {
                    if (!in_array($filter, array_keys($this->filters)))
                    {
                        abort(404);
                    }
                }
            }
        }
    }

    public function getModelProperty()
    {
        return $this->getClass()::$model;
    }
    
    public function getItemsProperty()
    {
        return $this->cache(function () {
            if ($this->type == 'reorder')
            {
                return $this->applySorting($this->applyFiltering($this->applySelect($this->applyWith($this->itemsQuery))))->{$this->paginate}(1000);
            }
            else
            {
                return $this->applySorting($this->applyGroupBy($this->applySearch($this->applyFiltering($this->applySelect($this->applyWith($this->itemsQuery))))))->{$this->paginate}($this->perPage);
            }
        });
    }

    public function getPaginateProperty()
    {
        if(isset($this->index->paginate)) {
            if($this->index->paginate == 'standard') {
                $this->paginate = 'paginate';
            } if($this->index->paginate == 'simple') {
                $this->paginate = 'simplePaginate';
            } 
        } else {
            $this->paginate = 'paginate';
        }

        return $this->paginate;
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
        $this->selectPage = false;
    }

    public function updatedPerPage()
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
        $this->selectPage = false;
    }

    public function bulkDelete()
    {
        $this->showDeleteModal = ! $this->showDeleteModal;
    }

    public function delete($id)
    {
        $this->item_to_be_deleted = $id;
        $this->showDeleteModal = ! $this->showDeleteModal;
    }

    public function destroy()
    {
        $this->executeDestroy();
        
        $this->resetPage();
        $this->selected = ($this->item_to_be_deleted) ? $this->selected: [];
        $this->showDeleteModal = false;
        $this->notify('success', 'Successfully deleted.');
    }

    public function executeDestroy()
    {
        $deleteItems = new \stdClass();

        if ($this->item_to_be_deleted)
        {
            $deleteItems = (clone $this->itemsQuery)->where('id', $this->item_to_be_deleted);
            $this->authorize('delete', [$deleteItems->first(), $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module]);
            $this->validate();
        }
        else
        {
            $deleteItems = $this->selectedItemsQuery;
            $this->authorize('bulkDelete', [$this->model, $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module]);
        }

        $className = 'App\\Actions\\'.class_basename($this->model).'\\'.str_replace(' ', '', ucwords(str_replace('-', ' ', Str::singular($this->moduleSection)))).str_replace(' ', '', ucwords(str_replace('-', ' ', Str::singular($this->moduleGroup)))).str_replace(' ', '', ucwords(str_replace('-', ' ', Str::singular($this->module)))).'DestroyAction';
        
        if (!class_exists($className))
        {
            $className = 'Formation\\Actions\\DestroyAction';
        }
        
        (new $className)->execute($deleteItems);
    }

    public function updatedShowDeleteModal()
    {
        if ($this->showDeleteModal === false)
        {
            $this->item_to_be_deleted = '';
        }
    }

    public function show($id, $tab=1)
    {
        $this->type = 'show';
        $this->tab = $tab;
        $this->formId = $id;
        $this->resetValidation();
        unset($this->editing);
        unset($this->editedSubClassFields);
        unset($this->subClassItems);
    }

    public function executeShow()
    {
        $this->editing = $this->model::findOrFail($this->formId);

        $withs = [];
        $fields[] = 'id';
        
        foreach ($this->form->items as $tab)
            foreach ($tab->items as $card)
                foreach ($card->items as $section)
                    foreach ($section->items as $column)
                        foreach ($column->items as $field) {
                            
                            if ($field->type != 'subfieldBox') {
                                $fields[] = $field->name;
                            }

                            if ($field->with) {
                                $query = $this->model::make();
                                $withItems = explode('.', $field->with);
                                $currentWith = '';

                                foreach ($withItems as $withItem) {
                                    $previousWith = $currentWith;
                                    $currentWith .= ($currentWith == '') ? $withItem : '.'.$withItem;
                                    $relationship = $query->$withItem();
                                    $relationshipName = class_basename($relationship);

                                    if ($relationshipName === 'BelongsTo') {
                                        $previousWithColumn = $relationship->getForeignKeyName();
                                        $previousWithMorphColumn = null;
                                        $currentWithColumn = $relationship->getOwnerKeyName();
                                    } elseif ($relationshipName === 'MorphTo') {
                                        $previousWithColumn = $relationship->getForeignKeyName();
                                        $previousWithMorphColumn = $relationship->getMorphType();
                                        $currentWithColumn = 'id';
                                    } elseif ($relationshipName === 'HasOne' || $relationshipName === 'MorphOne' || $relationshipName === 'HasMany') {
                                        $previousWithColumn = $relationship->getLocalKeyName();
                                        $previousWithMorphColumn = null;
                                        $currentWithColumn = $relationship->getForeignKeyName();
                                    }

                                    if ($previousWith === '' && isset($previousWithMorphColumn))
                                    {
                                        $fields[] = $previousWithMorphColumn;
                                    }

                                    if ($previousWith !== '' && (!isset($withs[$previousWith]) || !in_array($previousWithColumn, $withs[$previousWith])))
                                    {
                                        $withs[$previousWith][] = $previousWithColumn;
                                    }

                                    if ($previousWith !== '' && (!isset($withs[$previousWith]) || !in_array($previousWithMorphColumn, $withs[$previousWith])) && isset($previousWithMorphColumn))
                                    {
                                        $withs[$previousWith][] = $previousWithMorphColumn;
                                    }

                                    if (!isset($withs[$currentWith]) || !in_array($currentWithColumn, $withs[$currentWith]))
                                    {
                                        $withs[$currentWith][] = $currentWithColumn;
                                    }
                                    
                                    $query = $query->$withItem()->getRelated();
                                }
                                
                                if ($field->type == 'subfieldBox') {
                                    foreach ($field->items as $subfield) {
                                        $withs[$currentWith][] = $subfield->name;
                                    }
                                }
                                else {
                                    $withs[$currentWith][] = $field->reference;
                                }
                            }
                        }

        foreach ($withs as $key => $with) {
            $withReferences[] = $key.':'.implode(',', $with);
        }

        $query = $this->model::make();

        if (isset($withReferences))
            $query = $query->with($withReferences);

        $this->editing = $query->findOrFail($this->formId, $fields);
        $this->authorize('view', [$this->editing, $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);

        $this->useCachedRows(); 
    }

    public function save()
    {
        try {
            $this->executeSave();

            if ($this->form->redirectView == 'index') {
                if (count($this->form->items) != $this->tab) {
                    $this->tab++;
                    $this->edit($this->editing->id, $this->tab);
                }
                else {
                    $this->type = 'index';
                }

                $this->forgetComputed('form');
            }
            elseif ($this->form->redirectView == 'edit' || $this->form->redirectView == 'create') {
                $this->type = $this->form->redirectView;
                
                unset($this->editedSubClassFields);
                unset($this->subClassItems);
                $this->executeEdit();
            }
            else {
                $this->type = $this->form->redirectView;
                $this->forgetComputed('form');
            }
            
            $this->notify('success', 'Successfully saved.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions to let Livewire handle them
            throw $e;
        } catch (\Exception $e) {
            // Handle other exceptions and show error notification
            $this->notify('error', 'Failed to save.', $e->getMessage());
        }
    }

    public function executeSave()
    {
        if ($this->type === 'create')
            $this->authorize('create', [$this->model, $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);

            

        if ($this->type === 'edit')
            $this->authorize('update', [$this->editing, $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);
            foreach ($this->form->items as $tab)
                foreach ($tab->items as $card)
                    foreach ($card->items as $section)
                        foreach ($section->items as $column)
                            foreach ($column->items as $field)
                            {
                                $fieldName = $field->name;
                                
                                if ($field->type === "checkboxMultiple" || $field->type === "checkboxButtonMultiple")
                                {
                                    if (isset($this->editing->$fieldName))
                                    {
                                        // Convert pipe-separated string to array, then filter empty values
                                        $value = $this->editing->$fieldName;
                                        if (is_string($value)) {
                                            $value = explode('|', $value);
                                        }
                                        $this->editing->$fieldName = array_filter($value);
                                    }
                                }

                                // Normalize translatable fields: accept plain string and mirror to en/ms
                                if ($this->isModelTranslatable($this->editing, $fieldName))
                                {
                                    if (isset($this->editing->$fieldName) && is_string($this->editing->$fieldName)) {
                                        $this->editing->$fieldName = ['en' => $this->editing->$fieldName, 'ms' => $this->editing->$fieldName];
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

        foreach ($this->form->items as $tab)
            foreach ($tab->items as $card)
                foreach ($card->items as $section)
                    foreach ($section->items as $column)
                        foreach ($column->items as $field)
                        {
                            $fieldName = $field->name;

                            if ($field->type === "checkboxMultiple")
                            {
                                if (isset($this->editing->$fieldName))
                                    $temp = $this->editing->$fieldName;
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

    private function saveAttachment($defaultEditing){

        $object = $this->model::find($this->editing->id);

        foreach($this->fileColumnNames as $fileColumnName){
            $file = $defaultEditing->{$fileColumnName};
            if($file instanceof TemporaryUploadedFile) {
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


    public function gotoPage($page)
    {
        $this->setPage($page);
        $this->selectPage = $this->selectAll ? true : false;
    }

    public function create()
    {
        $this->type = 'create';
        $this->tab = 1;
        $this->resetValidation();
    }

    public function executeCreate()
    {
        $this->formId = null;
        $this->authorize('create', [$this->model, $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);
        $this->useCachedRows();
        $this->editing = $this->model::make();
    }

    public function reorder()
    {
        $this->type = 'reorder';
    }

    public function executeReorder()
    {
        $this->authorize('reorder', [$this->model, $this->itemId]);
    }

    public function setOrderedList($orderedList)
    {
        if(sizeof($this->items) > 0 && sizeof($orderedList) > 0)
        {
            $arr = [];

            foreach($orderedList as $orderedListItem)
            {
                $arr[] = $this->items->find($orderedListItem['value']);
            }

            $this->items->setCollection(collect($arr));
        }     
        
        $this->orderedList = $orderedList;
    }

    public function saveReorder()
    {
        $this->authorize('reorder', [$this->model]);
        
        foreach ($this->orderedList as $orderedId)
        {
            $this->model::where('id', $orderedId['value'])
            ->update(['order' => $orderedId['order']]);
        }

        $this->type = 'index';
    }

    public function getItemsQueryProperty()
    {
        return $this->applyGuard($this->model::query());
    }

    public function applySelect($query)
    {
        $model = $this->model::make();
        
        if (!isset($this->index->groupBy->items))
            $selects = ['id'];

        if (isset($this->index->select) && isset($this->index->select->items)) {
            foreach ($this->index->select->items as $item)
        {            
            if ($item->with) {
                $withItems = explode('.', $item->with);
                $relationship = class_basename(get_class($model->{$withItems[0]}()));

                if ($relationship === 'MorphTo') {
                    $selects[] = $model->{$withItems[0]}()->getMorphType();
                }
                else {
                    $selects[] = $item->name;
                }
            }

            if ($item->raw) {
                $selects[] = DB::raw($item->raw);
            }
            else {
                $selects[] = $item->name;
            }
        }
        }
        
        return $query->select($selects);
    }

    public function applyExport($query)
    {
        $model = $this->model::make();

        if (!isset($this->index->groupBy->items))
            $selects = [];

        if (isset($this->index->export) && isset($this->index->export->items)) {
            foreach ($this->index->export->items as $item)
            {
                if ($item->with) {
                    $withItems = explode('.', $item->with);
                    $relationship = class_basename(get_class($model->{$withItems[0]}()));

                    if ($relationship === 'MorphTo') {
                        $selects[] = $model->{$withItems[0]}()->getMorphType();
                    }
                    else {
                        $selects[] = $item->name;
                    }
                }

                if ($item->raw) {
                    $selects[] = DB::raw($item->raw);
                }
                else {
                    $selects[] = $item->name;
                }
            }
        }
        
        return $query->select($selects);
    }

    public function applyWith($query)
    {   
        $withs = [];

        if (isset($this->index->select) && isset($this->index->select->items)) {
            foreach ($this->index->select->items as $item) {
            if ($item->with) {
                $model = $this->model::make();
                $withItems = explode('.', $item->with);
                $currentWith = '';

                foreach ($withItems as $withItem) {
                    $previousWith = $currentWith;
                    $currentWith .= ($currentWith == '') ? $withItem : '.'.$withItem;
                    $relationship = $model->$withItem();
                    $relationshipName = class_basename($relationship);

                    if ($relationshipName === 'BelongsTo') {
                        $previousWithColumn = $relationship->getForeignKeyName();
                        $previousWithMorphColumn = null;
                        $currentWithColumn = $relationship->getOwnerKeyName();
                    } elseif ($relationshipName === 'MorphTo') {
                        $previousWithColumn = $relationship->getForeignKeyName();
                        $previousWithMorphColumn = $relationship->getMorphType();
                        $currentWithColumn = 'id';
                    } elseif ($relationshipName === 'HasOne' || $relationshipName === 'MorphOne') {
                        $previousWithColumn = $relationship->getLocalKeyName();
                        $previousWithMorphColumn = null;
                        $currentWithColumn = $relationship->getForeignKeyName();
                    }

                    if ($previousWith !== '' && (!isset($withs[$previousWith]) || !in_array($previousWithColumn, $withs[$previousWith])))
                    {
                        $withs[$previousWith][] = $previousWithColumn;
                    }

                    if ($previousWith !== '' && (!isset($withs[$previousWith]) || !in_array($previousWithMorphColumn, $withs[$previousWith])) && isset($previousWithMorphColumn))
                    {
                        $withs[$previousWith][] = $previousWithMorphColumn;
                    }

                    if (!isset($withs[$currentWith]) || !in_array($currentWithColumn, $withs[$currentWith]))
                    {
                        $withs[$currentWith][] = $currentWithColumn;
                    }
                    
                    $model = $model->$withItem()->getRelated();
                }

                $withs[$currentWith][] = $item->reference;
            }
        }
        }

        foreach ($withs as $key => $with) {
            $withReferences[] = $key.':'.implode(',', $with);
        }
       
        return isset($withReferences) ? $query->with($withReferences) : $query;
    }

    public function applyGuard($query)
    {
        if (isset($this->index->guard->items))
        {
            foreach ($this->index->guard->items as $guard)
            {
                if ($guard->with)
                {
                    $query->whereHas($guard->with, function ($query) use ($guard) {
                        if($guard->operator === 'in')
                            $query->whereIn($guard->reference, $guard->value);
                        else
                            $query->where($guard->reference, $guard->operator, $guard->value);
                    });
                }
                else {
                    if($guard->operator === 'in')
                        $query->whereIn($guard->name, $guard->value);
                    else
                        $query->where($guard->name, $guard->operator, $guard->value);
                }
            }
        }

        return $query;
    }

    public function applyGroupBy($query)
    {
        if (isset($this->index->groupBy->items))
        {
            foreach ($this->index->groupBy->items as $groupBy)
            {
                $query->groupBy($groupBy->name);
            }
        }
        
        return $query;
    }

    public function edit($id, $tab=1)
    {
        $this->type = 'edit';
        $this->formId = $id;
        $this->tab = $tab;
        $this->resetValidation();
        unset($this->editing);
        unset($this->editedSubClassFields);
        unset($this->subClassItems);
    }

    public function executeEdit()
    {
        $this->editing = $this->model::findOrFail($this->formId);

        $withs = [];
        $fields[] = 'id';
        
        foreach ($this->form->items as $tab)
            foreach ($tab->items as $card)
                foreach ($card->items as $section)
                    foreach ($section->items as $column)
                        foreach ($column->items as $field) {
                            
                            if ($field->type != 'subfieldBox') {
                                $fields[] = $field->name;
                            }

                            if ($field->with) {
                                $query = $this->model::make();
                                $withItems = explode('.', $field->with);
                                $currentWith = '';

                                foreach ($withItems as $withItem) {
                                    $previousWith = $currentWith;
                                    $currentWith .= ($currentWith == '') ? $withItem : '.'.$withItem;
                                    $relationship = $query->$withItem();
                                    $relationshipName = class_basename($relationship);

                                    if ($relationshipName === 'BelongsTo') {
                                        $previousWithColumn = $relationship->getForeignKeyName();
                                        $previousWithMorphColumn = null;
                                        $currentWithColumn = $relationship->getOwnerKeyName();
                                    } elseif ($relationshipName === 'MorphTo') {
                                        $previousWithColumn = $relationship->getForeignKeyName();
                                        $previousWithMorphColumn = $relationship->getMorphType();
                                        $currentWithColumn = 'id';
                                    } elseif ($relationshipName === 'HasOne' || $relationshipName === 'MorphOne' || $relationshipName === 'HasMany') {
                                        $previousWithColumn = $relationship->getLocalKeyName();
                                        $previousWithMorphColumn = null;
                                        $currentWithColumn = $relationship->getForeignKeyName();
                                    }

                                    if ($previousWith === '' && isset($previousWithMorphColumn))
                                    {
                                        $fields[] = $previousWithMorphColumn;
                                    }

                                    if ($previousWith !== '' && (!isset($withs[$previousWith]) || !in_array($previousWithColumn, $withs[$previousWith])))
                                    {
                                        $withs[$previousWith][] = $previousWithColumn;
                                    }

                                    if ($previousWith !== '' && (!isset($withs[$previousWith]) || !in_array($previousWithMorphColumn, $withs[$previousWith])) && isset($previousWithMorphColumn))
                                    {
                                        $withs[$previousWith][] = $previousWithMorphColumn;
                                    }

                                    if (!isset($withs[$currentWith]) || !in_array($currentWithColumn, $withs[$currentWith]))
                                    {
                                        $withs[$currentWith][] = $currentWithColumn;
                                    }
                                    
                                    $query = $query->$withItem()->getRelated();
                                }
                                
                                if ($field->type == 'subfieldBox') {
                                    foreach ($field->items as $subfield) {
                                        $withs[$currentWith][] = $subfield->name;
                                    }
                                }
                                else {
                                    $withs[$currentWith][] = $field->reference;
                                }
                            }
                        }

        foreach ($withs as $key => $with) {
            $withReferences[] = $key.':'.implode(',', $with);
        }

        $query = $this->model::make();

        if (isset($withReferences))
            $query = $query->with($withReferences);

        $this->editing = $query->findOrFail($this->formId, $fields);
        $this->authorize('update', [$this->editing, $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);

        foreach ($withs as $key => $with) {
            if(isset($this->editedSubClassFields[$with])) {
                $this->editedSubClassFields[$with] = $this->editing->$key;
            }
        }
        
        foreach ($this->form->items as $tab)
            foreach ($tab->items as $card)
                foreach ($card->items as $section)
                    foreach ($section->items as $column)
                        foreach ($column->items as $field)
                        {
                            $fieldName = $field->name;

                            if ($field->type === "checkboxMultiple")
                            {
                                
                                $arr = explode('|', $this->editing->$fieldName);
                                $this->editing->$fieldName = array_combine($arr, $arr);

                                $arr2 = [];
                                foreach ($field->options as $option) 
                                {
                                    $arr2[$option->name] = $option->name;
                                }

                                $this->editing->$fieldName = array_intersect(array_combine($arr, $arr), $arr2);
                            }
                        }

        $this->useCachedRows();
    }

    public function export()
    {
        return response()->streamDownload(function() {
            $headerFlag = true;
            $this->applySorting($this->applyGroupBy($this->applySearch($this->applyFiltering($this->applyExport($this->applyWith($this->itemsQuery))))))->chunkById(1000, function ($results) use (&$headerFlag) {
                if ($results->count() < 1) return;

                if ($headerFlag == true) {
                    echo implode(',', collect($this->index->export->items)->map(function ($exportSelectItem) {
                        return __($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$exportSelectItem->label);
                    })->toArray());
                    echo "\n";
                }

                $headerFlag = false;
                
                $results->map(function ($item) {
                    echo implode(',', collect($this->index->export->items)->map(function ($exportSelectItem) use ($item) {
                        if ($exportSelectItem->with) {
                            $tempItem = $item;
                            $withItems = explode('.', $exportSelectItem->with);
    
                            foreach ($withItems as $withItem) {
                                $tempItem = $tempItem->$withItem;
    
                                if (!isset($tempItem))
                                    break;
                            }
    
                            if($this->isModelTranslatable($tempItem, $exportSelectItem->reference)) {
                                $value = $tempItem->getTranslation($exportSelectItem->reference, $exportSelectItem->lang ?? App::getFallbackLocale());
                            } else {
                                $value = $tempItem->{$exportSelectItem->reference} ?? null;
                            }
                        }
                        else {
                            if($this->isModelTranslatable($item, $exportSelectItem->name)) {
                                $value = $item->getTranslation($exportSelectItem->name, $exportSelectItem->lang ?? App::getFallbackLocale());
                            } else {
                                $value = $item->{$exportSelectItem->name} ?? null;
                            }
                        }
                        
                        if($exportSelectItem->localize) {
                            $value = $value ? __($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$value) : '';
                        }      
                        else {
                            $value = $value ?? '';
                        }

                        return '"'.$value.'"'; 
                    })->toArray());
                    echo "\n";
                });
            }, $column = 'id');
        }, collect(explode('\\', $this->model))->last().date('YmdHis').'.csv');
    }

    public function custom($path, $itemId, $itemType = '')
    {
        redirect($path.'?itemId='.$itemId.($itemType ? '&itemType='.$itemType : ''));
    }

    public function tab($filters)
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
        $this->selectPage = false;
        $this->search = '';
        $this->showFilter = false;
        $this->filters = [];
        $this->sorts = [];

        foreach($filters as $key => $value)
        {
            $this->filters[$key] = $value;
        }
    }

    public function addField($field)
    {
        if (empty($this->editing->$field))
        {
            $temp[] = '';
            $this->editing->$field = $temp;
        }
        
        if (!empty($this->editing->$field))
        {
            $temp = $this->editing->$field;
            $temp[] = '';
            $this->editing->$field = $temp;
        }
    }

    public function removeField($field, $key)
    {
        if (empty($this->editing->$field))
        {
            $temp[] = '';
            $this->editing->$field = $temp;
        }
        
        $temp = $this->editing->$field;
        unset($temp[$key]);
        $temp = array_values($temp);
        $this->editing->$field = $temp;
    }

    public function addSubClassField($with, $headers)
    {
        $headers = explode(',', $headers);
        $this->subClassItems[$with][] = array_fill_keys($headers, '');  
    }

    public function removeSubClassField($with, $index)
    {
        unset($this->subClassItems[$with][$index]);
        $this->subClassItems[$with] = array_values($this->subClassItems[$with]);    
    }

    public function removeEditingSubField($field, $key)
    {
        if (empty($this->editing->$field))
        {
            $temp[] = '';
            $this->editing->$field = $temp;
        }
        
        if(!isset($this->editedSubClassFields[$field])){
            $this->editedSubClassFields[$field] = clone($this->editing->$field);
        } 
        else{
            $this->editedSubClassFields[$field] = $this->editedSubClassFields[$field];
        }

        unset($this->editedSubClassFields[$field][$key]);
    }

    public function notify(string $type, string $message, string $description=null)
    {
        $this->dispatchBrowserEvent('notify', ['type' => $type, 'message' => $message, 'description' => $description]);
    }

    public function setFiles($name, $files) {  
        $this->editing->{$name} = $files;
    }

    public function previewImage(string $url)
    {
        $this->previewImageUrl = $url;
        $this->showPreviewImageModal = true;
    }

    //TODO: move this to WithImageUpload trait
    public function getUploadingProgress(bool $isUploading): void
    {
        $this->isUploading = $isUploading;
    }

    public function updateFolderPath(string $name, string $folderPath): void
    {
        $this->folderPaths[$name] = $folderPath;
    }

    public function updateFileColumnName(string $name): void
    {
        $this->fileColumnNames[$name] = $name;
    }

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
                            $tempFiles[$file['position']] = TemporaryUploadedFile::unserializeFromLivewireRequest($file)['serializedFile'];
                        }
                    } elseif($category === 'existing') {
                        foreach($files as $file) {
                            $tempFiles[$file['position']] = $file['path'];
                        }
                    } else {
                        if(sizeof($this->serializeFiles) > 0 ){
                            if(isset($this->serializeFiles[$fieldName])) {
                                if(TemporaryUploadedFile::canUnserialize($this->serializeFiles[$fieldName])) {
                                    $tempFiles[] = TemporaryUploadedFile::unserializeFromLivewireRequest($this->serializeFiles[$fieldName]);
                                    
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

    public function serializeFiles($fieldName){
        if(is_string($this->editing->$fieldName)) {
            return $this->editing->$fieldName;
        }else {
            if($this->editing->$fieldName instanceof TemporaryUploadedFile) {
                return $this->editing->$fieldName->serializeForLivewireResponse();
            }
        }
    }

    public function import()
    {
        redirect('/'.$this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'/import?importType=import');
    }

    public function bulkEdit()
    {
        redirect('/'.$this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'/import?importType=bulkEdit');
    }

    public function getModuleFormation()
    {
        $moduleFormation = '\\App\\Formation\\'.Str::studly($this->moduleSection).'\\'.Str::studly($this->moduleGroup).'\\'.Str::studly(Str::singular($this->module)).'Formation';

        return (new $moduleFormation);
    }

    public function getModuleModel()
    {
        return $this->getModuleFormation()::$model;
    }

    public function getModuleForm()
    {
        return $this->getModuleFormation()->form($this);
    }

    public function getImportAttributes()
    {
        $importType = $this->importType;
        return $this->getModuleFormation()->index($this)->$importType;
    }

    public function getImportUniqueColumns()
    {
        return $this->getImportAttributes()->items[0]->uniqueColumns;
    }

    public function getImportColumns()
    {
        foreach ($this->getImportAttributes()->items as $item) {
            $columns[$item->name] = $item->label;
        }

        unset($columns[""]); 

        return $columns;
    }

    public function getModuleRules()
    {
        $rules = [];
        if ($this->type == "index") {
            foreach ($this->index->itemAction->items as $itemAction) {
                if ($itemAction->name == 'delete') {
                    $rules['item_to_be_deleted'] = $itemAction->rules;
                }
            }
        }
        else {
            foreach ($this->getModuleFormation()->form($this)->items as $tab)
                foreach ($tab->items as $card)
                    foreach ($card->items as $section)
                        foreach ($section->items as $column)
                            foreach ($column->items as $field)
                            {
                                if ($field->type === "preset")
                                    continue;

                                if ($field->type === "checkboxMultiple" || $field->type === "checkboxButtonMultiple") {
                                    $rules['editing.'.$field->name.'.*'] = $field->rules;
                                }
                                elseif ($field->type === "subfieldBox") {
                                    foreach ($field->items as $key => $subfield)
                                    {
                                        if ($subfield->type != "preset") {

                                            $fieldName = isset($field->with) ? $field->with : $field->name;

                                            if ($subfield->type === "checkboxMultiple") {
                                                $rules['editing.'.$fieldName.'.*.'.$subfield->name.'.*'] = $subfield->rules;
                                                $rules['subClassItems.'.$fieldName.'.*.'.$subfield->name.'.*'] = $subfield->rules;
                                                
                                                $this->validationAttributes['editing.'.$fieldName.'.*.'.$subfield->name.'.*'] = __($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$field->name) .' '. strtolower(__($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$subfield->name));
                                                $this->validationAttributes['subClassItems.'.$fieldName.'.*.'.$subfield->name.'.*'] = __($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$field->name) .' '. strtolower(__($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$subfield->name));
                                            }else {
                                                $rules['editing.'.$fieldName.'.*.'.$subfield->name] = $subfield->rules;
                                                $rules['subClassItems.'.$fieldName.'.*.'.$subfield->name] = $subfield->rules;
                                                
                                                $this->validationAttributes['editing.'.$fieldName.'.*.'.$subfield->name] = __($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$field->name) .' '. strtolower(__($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$subfield->name));
                                                $this->validationAttributes['subClassItems.'.$fieldName.'.*.'.$subfield->name] = __($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$field->name) .' '. strtolower(__($this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'.'.$subfield->name));                 
                                            }                                            
                                        }
                                    }
                                }
                                else {
                                    // Handle translatable model fields (web vs API)
                                    $modelInstance = app($this->getModelProperty());
                                    if ($this->isModelTranslatable($modelInstance, $field->name)) {
                                        $isApi = \Illuminate\Support\Facades\Request::is('api/*');
                                        if ($isApi) {
                                            // For API, validate the root key (e.g., editing.name)
                                            $rules['editing.'.$field->name] = $field->rules;
                                        } else {
                                            // For web, validate specific locale (e.g., editing.name.en)
                                            $lang = $field->lang ?? (\Illuminate\Support\Facades\App::getFallbackLocale() ?: 'en');
                                            $rules['editing.'.$field->name.'.'.$lang] = $field->rules;
                                        }
                                    } else {
                                        $rules['editing.'.$field->name] = $field->rules;
                                    }
                                }
                            }
        }

        return $rules;
    }

    public function getImportChunkSize()
    {
        return $this->getImportAttributes()->chunkSize;
    }

    public function guessWhichColumnsMapToWhichFields()
    {
        foreach ($this->getImportColumns() as $column) {
            $guesses[$column] = [$column];
        }

        foreach ($this->columns as $column) {
            $match = collect($guesses)->search(fn($options) => in_array(strtolower($column), $options));

            if ($match) $this->fieldColumnMap[$match] = $column;
        }
    }

    public function back()
    {
        redirect('/'.$this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module);
    }

    public function previous()
    {
        $records = collect($this->items->items());
        $currentIndex = $this->getCurrentIndex($records);

        if ($currentIndex === false) {
            $firstRecord = $records->first();
            $this->formId = $firstRecord['id'];
        } elseif ($currentIndex === 0) {
            if ($this->page > 1) {
                $this->previousPage();
                $previousRecord = collect($this->getItemsProperty()->items())->last();
                $this->formId = $previousRecord['id'];
            }
        } else {
            $previousRecord = $records[$currentIndex - 1];
            $this->formId = $previousRecord['id'];
        }

        $this->resetValidation();
        $this->resetErrorBag();
        unset($this->editing);
        unset($this->editedSubClassFields);
        unset($this->subClassItems); 
    }

    public function next()
    {
        $records = collect($this->items->items());
        $currentIndex = $this->getCurrentIndex($records);

        if ($currentIndex === false) {
            $firstRecord = $records->first();
            $this->formId = $firstRecord['id'];
        } elseif ($currentIndex === $records->count() - 1) {
            if ($this->page < $this->items->lastPage()) {
                $this->nextPage();
                $nextRecord = collect($this->getItemsProperty()->items())->first();
                $this->formId = $nextRecord['id'];
            }
        } else {
            $nextRecord = $records[$currentIndex + 1];
            $this->formId = $nextRecord['id'];
        }

        $this->resetValidation();
        $this->resetErrorBag();
        unset($this->editing);
        unset($this->editedSubClassFields);
        unset($this->subClassItems);  
    }

    public function getIsFirstRecordProperty()
    {
        $records = collect($this->items->items());
        $currentIndex = $this->getCurrentIndex($records);

        if ($this->page === 1 && $currentIndex === false) {
            $records = collect($this->getItemsProperty()->items());
            $currentIndex = $this->getCurrentIndex($records);
        }

        return $this->page === 1 && $currentIndex === 0;
    }

    public function getIsLastRecordProperty()
    {
        $records = collect($this->items->items());
        $currentIndex = $this->getCurrentIndex($records);
  
        $isLastPage = $records->count() < $this->items->perPage();

        return $isLastPage && $currentIndex === $records->count() - 1;
    }

    private function getCurrentIndex($records)
    {
        return $records->pluck('id')->search($this->formId);
    }

    private function isModelTranslatable($model, string $column): bool {
        return in_array(\Spatie\Translatable\HasTranslations::class, class_uses_recursive($model::class)) && $model->isTranslatableAttribute($column);
    }
    
    public function selectionChanged(string $name, string $source, array $old, array $new) {
        if($source == 'editing') {
            $this->editing->{$name} = $new;
        }
    }
}