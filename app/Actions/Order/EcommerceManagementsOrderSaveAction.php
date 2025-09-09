<?php

namespace App\Actions\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class EcommerceManagementsOrderSaveAction
{
    /**
     * Execute the save action for orders
     *
     * @param object $data The order data
     * @param string $type The action type (create or edit)
     * @return int The order ID
     */
    public function execute($data, $type = 'create')
    {
        // Set audit fields
        if ($type === 'create') {
            $data->created_by = Auth::id();
        }
        $data->updated_by = Auth::id();

        // Set default values if not provided
        if (empty($data->currency)) {
            $data->currency = 'MYR';
        }

        if (empty($data->subtotal)) {
            $data->subtotal = 0;
        }

        if (empty($data->tax)) {
            $data->tax = 0;
        }

        if (empty($data->shipping)) {
            $data->shipping = 0;
        }

        if (empty($data->discount)) {
            $data->discount = 0;
        }

        if (empty($data->total)) {
            $data->total = 0;
        }

        if (empty($data->total_price)) {
            $data->total_price = $data->total;
        }

        if (empty($data->placed_at)) {
            $data->placed_at = now();
        }

        // Convert to array for mass assignment
        $orderData = $data->toArray();
        
        // Remove order items from the data as they're handled separately
        unset($orderData['orderItems']);

        if ($type === 'create') {
            $order = Order::create($orderData);
        } else {
            $order = Order::findOrFail($data->id);
            $order->update($orderData);
        }

        return $order->id;
    }
}
