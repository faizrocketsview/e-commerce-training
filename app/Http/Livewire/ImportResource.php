<?php

namespace App\Http\Livewire;

use Formation\DataTable\WithDataTable;
use Illuminate\Support\Str;
use Formation\DataTable\WithImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ImportResource extends Component
{
    use WithFileUploads;
    
    use WithDataTable {
        render as WithDataTableRender;
    }

    protected $listeners = ['updateFolderPath', 'updateFileColumnName', 'setFiles', 'previewImage'];
    
    protected $queryString = ['itemId', 'itemType', 'sorts', 'search', 'filters', 'showFilter', 'perPage', 'type', 'formId', 'tab', 'importType'];

    public $view = [
        'index' => 'formation.import.index',
        'form' => 'formation.import.form',
        'reorder' => 'formation.import.reorder',
    ];

    public function render()
    {
        $this->validateIndexTabFilters();
        
        if ($this->type === 'create')
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
                'attachment' => $this->attachment,
                'importColumns' => $this->importColumns,
                'columns' => $this->columns,
                'fieldColumnMap' => $this->fieldColumnMap,
            ]);
        }

        return $this->WithDataTableRender();
    }

    public function getModelProperty()
    {
        if ($this->importType == 'import')
            return 'App\Models\Import';

        if ($this->importType == 'bulkEdit')
            return 'App\Models\BulkEdit';
    }

    public function getClass()
    {
        if (Str::plural($this->module) === $this->module) 
        {
            $module =  Str::singular($this->module);
            $class = '\\App\\Formation\\ImportFormation';


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

    public function executeSave()
    {
        $this->resetValidation();

        if ($this->editing->attachment == null) {
            Validator::make(
                ['attachment' => $this->editing],
                ['attachment' => ['required', File::types(['csv'])]],
            )->validate();
        }

        $this->editing->attachment = $this->unserializeFiles($this->editing->attachment, 'attachment');
        $this->serializeFiles['attachment'] = $this->serializeFiles('attachment');        
        $file = $this->editing->attachment;
        $this->editing->offsetUnset('attachment');

        $filename = $file->getClientOriginalName();
        $path = $file->storePubliclyAs('/imports', $filename, 's3');

        $import = $this->getModelProperty()::create([
            'model' => $this->getModuleModel(),
            'file_name' => $filename,
            'file_path' => $path,
            'status' => 'new',
            'total_inserted' => 0,
            'total_failed' => 0,
            'total_rows' => 0,
            'total_completed_chunk' => 0,
            'total_chunk' => 0,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
        
        Excel::import(new WithImport(
            $this->moduleSection, 
            $this->moduleGroup, 
            $this->module, 
            $this->getModuleModel(), 
            $this->fieldColumnMap, 
            $this->importType, 
            $this->getImportChunkSize(), 
            $import, 
            Auth::user()), $path, 's3');

        return $this->editing;
    }
    
    public function executeIndex()
    {
        $this->formId = null;
        $this->tab = null;
        unset($this->editing);
        unset($this->editedSubClassFields);
        unset($this->subClassItems);    
        unset($this->attachment);    
        unset($this->importColumns);    
        unset($this->columns);    
        unset($this->fieldColumnMap);    
        $this->authorize('viewAny', [$this->getModuleModel(), $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);
        $this->sortByDefault();
    }

    public function executeCreate()
    {
        $this->formId = null;
        $this->authorize('create', [$this->getModuleModel(), $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);
        $this->useCachedRows();
        $this->editing = $this->model::make();
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
        $this->authorize('viewAny', [$this->getModuleModel(), $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);

        $this->useCachedRows(); 
    }

    public function updateFileColumnName(string $name): void
    {
        $this->fileColumnNames[$name] = $name;

        $this->resetValidation();
        $this->importColumns = [];    
        $this->columns = [];    
        $this->fieldColumnMap = [];  

        $path = $this->editing->attachment['temporary'][0]['path'];

        $this->importColumns = $this->getImportColumns();
        $this->columns = (new HeadingRowImport())->toArray($path)[0][0];

        $this->guessWhichColumnsMapToWhichFields();
    }
}
