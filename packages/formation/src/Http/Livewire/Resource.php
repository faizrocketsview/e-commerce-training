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
}
