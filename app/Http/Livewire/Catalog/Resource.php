<?php

namespace App\Http\Livewire\Catalog;

use Formation\DataTable\WithDataTable;
use Livewire\Component;

class Resource extends Component
{
    use WithDataTable;

    protected $listeners = ['updateFolderPath', 'updateFileColumnName', 'setFiles', 'previewImage', 'selectionChanged'];
    
    protected $queryString = ['itemId', 'itemType', 'sorts', 'search', 'filters', 'showFilter', 'perPage', 'type', 'formId', 'tab'];

    public $view = [
        'index' => 'catalog.products-index',
        'form' => 'catalog.product-form',
        'reorder' => 'formation.reorder',
    ];

    public function mount(): void
    {
        // Bind to products module explicitly for catalog frontend
        $this->module = 'products';
        $this->moduleGroup = 'managements';
        $this->moduleSection = 'ecommerce';
    }

    public function rules(): array
    {
        // Catalog is view-only in this component; skip validation rule resolution
        return [];
    }
}


