<?php

namespace App\Http\Livewire\Product;

use App\Http\Livewire\Resource as BaseResource;

class Resource extends BaseResource
{
    public function mount($module = 'products', $moduleGroup = 'managements', $moduleSection = 'ecommerce')
    {
        parent::mount($module, $moduleGroup, $moduleSection);
    }

    /**
     * Check if model is translatable for a specific column
     */
    public function isModelTranslatable($model, string $column): bool 
    {
        // Check if Spatie Translatable package is available
        if (!class_exists(\Spatie\Translatable\HasTranslations::class)) {
            return false;
        }
        
        return in_array(\Spatie\Translatable\HasTranslations::class, class_uses_recursive($model::class)) && $model->isTranslatableAttribute($column);
    }
}
