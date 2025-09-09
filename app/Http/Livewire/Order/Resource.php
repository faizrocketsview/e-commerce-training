<?php

namespace App\Http\Livewire\Order;

use App\Http\Livewire\Resource as BaseResource;
use App\Models\Product;
use Illuminate\Support\Str;

class Resource extends BaseResource
{
    // Holds dynamic subfieldBox items (e.g., orderItems)
    public $subClassItems = [];
    // Holds existing subfield records when editing/showing
    public $editedSubClassFields = [];

    public function mount($module = 'orders', $moduleGroup = 'managements', $moduleSection = 'ecommerce')
    {
        parent::mount($module, $moduleGroup, $moduleSection);

        // Ensure subClassItems is always initialized to avoid PropertyNotFound
        if (!isset($this->subClassItems) || !is_array($this->subClassItems)) {
            $this->subClassItems = ['orderItems' => []];
        }
    }

    /**
     * Override rules method to add validation rules for order items
     */
    public function rules()
    {
        // Get rules from the WithDataTable trait
        $rules = $this->getModuleRules();
        
        // Add validation rules for order items
        $rules['subClassItems.orderItems'] = ['nullable', 'array'];
        $rules['subClassItems.orderItems.*.product_id'] = ['required', 'exists:products,id'];
        $rules['subClassItems.orderItems.*.quantity'] = ['required', 'integer', 'min:1'];
        
        return $rules;
    }

    /**
     * Override executeSave to handle order items synchronization
     */
    public function executeSave()
    {
        if ($this->type === 'create')
            $this->authorize('create', [$this->model, $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);

        if ($this->type === 'edit')
            $this->authorize('update', [$this->editing, $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);

        // Process order items before saving
        $orderItemsData = [];
        if (isset($this->subClassItems['orderItems'])) {
            $orderItemsData = $this->subClassItems['orderItems'];
        }

        // Process other form fields (excluding order items)
        foreach ($this->form->items as $tab)
            foreach ($tab->items as $card)
                foreach ($card->items as $section)
                    foreach ($section->items as $column)
                        foreach ($column->items as $field)
                        {
                            $fieldName = $field->name;
                            
                            // Skip order items - they're handled separately
                            if ($fieldName === 'orderItems') {
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

        // Process other form fields (excluding order items)
        foreach ($this->form->items as $tab)
            foreach ($tab->items as $card)
                foreach ($card->items as $section)
                    foreach ($section->items as $column)
                        foreach ($column->items as $field)
                        {
                            $fieldName = $field->name;

                            // Skip order items - they're handled separately
                            if ($fieldName === 'orderItems') {
                                continue;
                            }

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

        // Remove order items from the object before saving
        unset($parentWithoutRelations->orderItems);

        foreach ($subClasses as $subClass) {
            unset($parentWithoutRelations->$subClass);
        }

        $this->editing->id = (new $className)->execute($parentWithoutRelations, $this->type); 
        if(sizeof($this->fileColumnNames) > 0){
            $this->saveAttachment($defaultEditing);
        }
        $this->executeSaveSubClass($subClasses);

        // Handle order items synchronization
        $this->syncOrderItems($orderItemsData);

        return $this->editing;
    }

    /**
     * Override executeEdit to load order items
     */
    public function executeEdit()
    {
        // Load the order without order items first
        $this->editing = $this->model::findOrFail($this->formId);
        // Ensure reactive containers exist
        if (!isset($this->subClassItems) || !is_array($this->subClassItems)) {
            $this->subClassItems = [];
        }
        if (!isset($this->editedSubClassFields) || !is_array($this->editedSubClassFields)) {
            $this->editedSubClassFields = [];
        }
        
        // Load order items separately
        $this->loadOrderItems();
        
        // Set authorization
        $this->authorize('update', [$this->editing, $this->moduleSection.'.'.$this->moduleGroup.'.'.$this->module, $this->itemId]);
        
        $this->useCachedRows();
    }

    /**
     * Load order items for editing
     */
    protected function loadOrderItems()
    {
        // Get the actual order with order items loaded
        $order = \App\Models\Order::with('orderItems.product')->find($this->editing->id);
        if (!$order) {
            return;
        }
        // Prepare array structure for edit mode (Formation uses editedSubClassFields in edit)
        $this->editedSubClassFields['orderItems'] = $order->orderItems->map(function($item){
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'sku' => $item->sku,
                'unit_price' => (float) $item->unit_price,
                'quantity' => (int) $item->quantity,
            ];
        })->toArray();
    }

    /**
     * Override executeCreate to initialize order items
     */
    public function executeCreate()
    {
        parent::executeCreate();
        
        // Initialize order items as empty array
        $this->subClassItems['orderItems'] = [];
    }

    /**
     * Override executeShow to load order items
     */
    public function executeShow()
    {
        $this->editing = $this->model::findOrFail($this->formId);
        // Ensure reactive containers exist
        if (!isset($this->subClassItems) || !is_array($this->subClassItems)) {
            $this->subClassItems = [];
        }
        if (!isset($this->editedSubClassFields) || !is_array($this->editedSubClassFields)) {
            $this->editedSubClassFields = [];
        }

        // For show view, ensure relation is Eloquent collection (not arrays)
        $this->editing->unsetRelation('orderItems');
        $this->editing->load('orderItems');

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

        // Load order items for display
        $this->loadOrderItems();

        $this->useCachedRows(); 
    }

    /**
     * Sync order items with the order
     */
    protected function syncOrderItems($orderItemsData)
    {
        if (empty($orderItemsData)) {
            return;
        }

        // Delete existing order items
        \App\Models\OrderItem::where('order_id', $this->editing->id)->delete();

        // Create new order items
        foreach ($orderItemsData as $itemData) {
            if (empty($itemData['product_id']) || empty($itemData['quantity'])) {
                continue;
            }

            $product = Product::find($itemData['product_id']);
            if (!$product) {
                continue;
            }

            $unitPrice = $itemData['unit_price'] ?? $product->price;
            $quantity = (int) $itemData['quantity'];

            \App\Models\OrderItem::create([
                'order_id' => $this->editing->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'created_by' => auth()->id(),
            ]);
        }

        // Recalculate order totals
        $this->recalculateOrderTotals();
    }

    /**
     * Recalculate order totals based on order items
     */
    protected function recalculateOrderTotals()
    {
        $order = \App\Models\Order::find($this->editing->id);
        $orderItems = \App\Models\OrderItem::where('order_id', $order->id)->get();
        
        $subtotal = $orderItems->sum('line_total');
        $tax = $subtotal * 0.06; // 6% tax rate - adjust as needed
        $shipping = 0; // Add shipping calculation logic if needed
        $discount = 0; // Add discount calculation logic if needed
        $total = $subtotal + $tax + $shipping - $discount;

        $order->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'discount' => $discount,
            'total' => $total,
            'total_price' => $total,
        ]);
    }

    /**
     * Recalculate order totals from current order items (for real-time updates)
     */
    protected function recalculateOrderTotalsFromItems()
    {
        if (!isset($this->subClassItems['orderItems']) || empty($this->subClassItems['orderItems'])) {
            return;
        }

        $subtotal = 0;
        foreach ($this->subClassItems['orderItems'] as $item) {
            if (isset($item['unit_price']) && isset($item['quantity']) && 
                is_numeric($item['unit_price']) && is_numeric($item['quantity'])) {
                $subtotal += $item['unit_price'] * $item['quantity'];
            }
        }

        $tax = $subtotal * 0.06; // 6% tax rate
        $shipping = $this->editing->shipping ?? 0;
        $discount = $this->editing->discount ?? 0;
        $total = $subtotal + $tax + $shipping - $discount;

        // Update the editing object with calculated values
        $this->editing->subtotal = $subtotal;
        $this->editing->tax = $tax;
        $this->editing->total = $total;
        $this->editing->total_price = $total;
    }

    /**
     * Add order item
     */
    public function addOrderItem()
    {
        if (!isset($this->subClassItems['orderItems'])) {
            $this->subClassItems['orderItems'] = [];
        }
        
        $this->subClassItems['orderItems'][] = [
            'product_id' => '',
            'product_name' => '',
            'sku' => '',
            'unit_price' => 0,
            'quantity' => 1,
        ];
    }

    /**
     * Remove order item
     */
    public function removeOrderItem($index)
    {
        if (isset($this->subClassItems['orderItems'][$index])) {
            unset($this->subClassItems['orderItems'][$index]);
            $this->subClassItems['orderItems'] = array_values($this->subClassItems['orderItems']);
        }
    }

    /**
     * Update order item when product is selected
     */
    public function updatedSubClassItems($value, $key)
    {
        if (str_contains($key, 'orderItems') && str_contains($key, 'product_id')) {
            $index = explode('.', $key)[1];
            if (isset($this->subClassItems['orderItems'][$index])) {
                $product = Product::find($value);
                if ($product) {
                    $this->subClassItems['orderItems'][$index]['product_name'] = $product->name;
                    $this->subClassItems['orderItems'][$index]['sku'] = $product->sku;
                    $this->subClassItems['orderItems'][$index]['unit_price'] = $product->price;
                    
                    // Set quantity to 1 if not set, or validate against stock
                    $currentQuantity = $this->subClassItems['orderItems'][$index]['quantity'] ?? 1;
                    if ($currentQuantity > $product->stock) {
                        $this->subClassItems['orderItems'][$index]['quantity'] = $product->stock;
                        $currentQuantity = $product->stock;
                    }
                    
                }
            }
        }

        if (str_contains($key, 'orderItems') && str_contains($key, 'quantity')) {
            $index = explode('.', $key)[1];
            if (isset($this->subClassItems['orderItems'][$index])) {
                $quantity = (int) $value;
                $productId = $this->subClassItems['orderItems'][$index]['product_id'] ?? null;
                
                // Validate quantity against stock
                if ($productId) {
                    $product = Product::find($productId);
                    if ($product && $quantity > $product->stock) {
                        $this->subClassItems['orderItems'][$index]['quantity'] = $product->stock;
                        $quantity = $product->stock;
                        $this->notify('warning', 'Quantity exceeds available stock. Set to maximum available: ' . $product->stock);
                    }
                }
                
            }
        }
        
        // Recalculate order totals whenever order items change
        $this->recalculateOrderTotalsFromItems();
    }

    /**
     * Handle updates to subClassItems property
     */
    public function updated($propertyName, $value)
    {
        if (str_contains($propertyName, 'subClassItems.orderItems')) {
            $parts = explode('.', $propertyName);
            if (count($parts) >= 4) {
                $index = $parts[2];
                $field = $parts[3];
                
                if ($field === 'product_id' && isset($this->subClassItems['orderItems'][$index])) {
                    $product = Product::find($value);
                    if ($product) {
                        $this->subClassItems['orderItems'][$index]['product_name'] = $product->name;
                        $this->subClassItems['orderItems'][$index]['sku'] = $product->sku;
                        $this->subClassItems['orderItems'][$index]['unit_price'] = $product->price;
                        
                        // Set quantity to 1 if not set, or validate against stock
                        $currentQuantity = $this->subClassItems['orderItems'][$index]['quantity'] ?? 1;
                        if ($currentQuantity > $product->stock) {
                            $this->subClassItems['orderItems'][$index]['quantity'] = $product->stock;
                        }
                    }
                }
                
                if ($field === 'quantity' && isset($this->subClassItems['orderItems'][$index])) {
                    $quantity = (int) $value;
                    $productId = $this->subClassItems['orderItems'][$index]['product_id'] ?? null;
                    
                    // Validate quantity against stock
                    if ($productId) {
                        $product = Product::find($productId);
                        if ($product && $quantity > $product->stock) {
                            $this->subClassItems['orderItems'][$index]['quantity'] = $product->stock;
                            $this->notify('warning', 'Quantity exceeds available stock. Set to maximum available: ' . $product->stock);
                        }
                    }
                }
                
                // Recalculate order totals whenever order items change
                $this->recalculateOrderTotalsFromItems();
            }
        }
    }

    /**
     * Handle updates to editing subfields (for existing order items)
     */
    public function updatedEditing($value, $key)
    {
        if (str_contains($key, 'orderItems') && str_contains($key, 'product_id')) {
            $parts = explode('.', $key);
            if (count($parts) >= 3) {
                $index = $parts[1];
                if (isset($this->editing->orderItems[$index])) {
                    $product = Product::find($value);
                    if ($product) {
                        $this->editing->orderItems[$index]->product_name = $product->name;
                        $this->editing->orderItems[$index]->sku = $product->sku;
                        $this->editing->orderItems[$index]->unit_price = $product->price;
                        
                        // Set quantity to 1 if not set, or validate against stock
                        $currentQuantity = $this->editing->orderItems[$index]->quantity ?? 1;
                        if ($currentQuantity > $product->stock) {
                            $this->editing->orderItems[$index]->quantity = $product->stock;
                            $currentQuantity = $product->stock;
                        }
                        
                    }
                }
            }
        }

        if (str_contains($key, 'orderItems') && str_contains($key, 'quantity')) {
            $parts = explode('.', $key);
            if (count($parts) >= 3) {
                $index = $parts[1];
                if (isset($this->editing->orderItems[$index])) {
                    $quantity = (int) $value;
                    $productId = $this->editing->orderItems[$index]->product_id ?? null;
                    
                    // Validate quantity against stock
                    if ($productId) {
                        $product = Product::find($productId);
                        if ($product && $quantity > $product->stock) {
                            $this->editing->orderItems[$index]->quantity = $product->stock;
                            $quantity = $product->stock;
                            $this->notify('warning', 'Quantity exceeds available stock. Set to maximum available: ' . $product->stock);
                        }
                    }
                    
                }
            }
        }
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
