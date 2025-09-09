<?php

namespace App\Http\Livewire;

use Formation\DataTable\WithDataTable;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class ImportErrorResource extends Component
{
    use WithDataTable, WithFileUploads;
    
    protected $listeners = ['updateFolderPath', 'updateFileColumnName', 'setFiles', 'previewImage'];
    
    protected $queryString = ['itemId', 'itemType', 'sorts', 'search', 'filters', 'showFilter', 'perPage', 'type', 'formId', 'tab', 'importType'];

    public $view = [
        'index' => 'formation.import-error.index',
        'form' => 'formation.import-error.form',
        'reorder' => 'formation.import-error.reorder',
    ];

    public function render()
    {
        $this->validateIndexTabFilters();
        $this->getModelProperty();
        
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
            'indexSelectItems' => $this->index->select->items,
            'filterItems' => isset($this->index->filter->items)?$this->index->filter->items:null,
            'searchItems' => isset($this->index->search->items)?$this->index->search->items:null,
            'actionItems' => isset($this->index->action->items)?$this->index->action->items:null,
            'itemActionItems' => isset($this->index->itemAction->items)?$this->index->itemAction->items:null,
            'indexTabItems' => isset($this->index->indexTab->items)?$this->index->indexTab->items:null,
            'module' => $this->module,
            'moduleGroup' => $this->moduleGroup,
            'moduleSection' => $this->moduleSection,
        ]);
    }

    public function getModelProperty()
    {
        if ($this->itemType == 'import'){
            $this->importType = 'import';
            return 'App\Models\ImportError';
        }

        if ($this->itemType == 'bulkEdit'){
            $this->importType = 'bulkEdit';
            return 'App\Models\BulkEditError';
        }
    }

    public function getClass()
    {
        if (Str::plural($this->module) === $this->module) 
        {
            $module =  Str::singular($this->module);
            // $class = '\\App\\Formation\\'.Str::studly($this->moduleSection).'\\'.Str::studly($this->moduleGroup).'\\'.Str::studly($module).'Formation';
            $class = '\\App\\Formation\\ImportErrorFormation';


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

    public function back()
    {
        redirect('/'.$this->moduleSection.'/'.$this->moduleGroup.'/'.$this->module.'/import?importType='.$this->importType);
    }

    public function executeIndex()
    {
        $this->formId = null;
        $this->tab = null;
        unset($this->editing);
        unset($this->editedSubClassFields);
        unset($this->subClassItems);    
        $this->authorize('viewAny', [$this->getModuleModel(), $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);
        $this->sortByDefault();
    }
}
