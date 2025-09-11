<?php

namespace App\Actions\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class EcommerceManagementsOrderSaveAction
{
    /**
     * Execute the save action for orders using an object-oriented approach.
     *
     * @param object $object The order data object (Formation editing object)
     * @param string|null $actionType Ignored (kept for compatibility with caller)
     * @return int The order ID
     */
    public function execute($object, $actionType = null)
    {
        // Audit
        if (empty($object->id)) {
            $object->created_by = Auth::id();
        }
        $object->updated_by = Auth::id();

        // Defaults via null-coalescing (respects 0 values and non-empty strings)
        $object->currency = $object->currency ?? 'MYR';
        $object->subtotal = $object->subtotal ?? 0;
        $object->tax = $object->tax ?? 0;
        $object->shipping = $object->shipping ?? 0;
        $object->discount = $object->discount ?? 0;
        $object->total = $object->total ?? 0;
        $object->total_price = $object->total_price ?? $object->total;
        $object->placed_at = $object->placed_at ?? now();

        // Remove sub-relations handled elsewhere
        if (isset($object->orderItems)) {
            unset($object->orderItems);
        }

        // Persist as Eloquent object (aligns with default SaveAction semantics)
        $order = empty($object->id) ? new Order() : Order::findOrFail($object->id);

        foreach (get_object_vars($object) as $attribute => $value) {
            $order->{$attribute} = $value;
        }

        $order->save();

        return $order->id;
    }
}
