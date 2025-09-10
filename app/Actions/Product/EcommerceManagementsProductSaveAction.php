<?php

namespace App\Actions\Product;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class EcommerceManagementsProductSaveAction
{
    /**
     * Create or update a product, handling translated attributes.
     *
     * @param object $data  Incoming data object from Formation
     * @param string $type  'create' | 'edit'
     * @return int          Saved product id
     */
    public function execute($data, string $type = 'create'): int
    {
        // Audit
        if ($type === 'create') {
            $data->created_by = Auth::id();
        }
        $data->updated_by = Auth::id();

        // Ensure translated fields accept both string and array
        foreach (['name', 'description'] as $translatedField) {
            if (isset($data->{$translatedField})) {
                // If a plain string is provided, wrap into ['en' => value]
                if (is_string($data->{$translatedField})) {
                    $data->{$translatedField} = ['en' => $data->{$translatedField}];
                }
            }
        }

        $payload = $data->toArray();

        if ($type === 'create') {
            $product = Product::create($payload);
        } else {
            $product = Product::findOrFail($data->id);
            $product->update($payload);
        }

        return $product->id;
    }
}


