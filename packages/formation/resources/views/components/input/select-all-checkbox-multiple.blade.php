<div 
    wire:loading.class="pointer-events-none opacity-50"
    x-data="{
        allSelected: @entangle('selectedItems'),
        items: @js($items),
        get isAllSelected() {
            return this.allSelected.length === this.items.length && this.items.length > 0;
        },
     }">
    
    <div class="mt-4 mb-4">
        {{-- Select All Checkbox --}}
        <div class="flex items-center space-x-3 pb-3 border-b border-gray-200">
            <div class="relative">
                <input 
                    type="checkbox" 
                    id="select-all-{{ $this->id }}"
                    class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 focus:ring-2"
                    :checked="isAllSelected"
                    @change="$wire.toggleAll()"
                    x-ref="selectAllCheckbox"
                >
            </div>
            <label for="select-all-{{ $this->id }}" class="text-sm font-medium text-gray-700 cursor-pointer select-none">
                Select All
                <span class="text-gray-500 font-normal ml-1">
                    (<span x-text="allSelected.length"></span> of <span x-text="items.length"></span>)
                </span>
            </label>
        </div>
    </div>

    {{-- Individual Checkboxes --}}
    <div class="space-y-3">
        @foreach($items as $item)
            <div class="flex items-center space-x-3">
                <input 
                    type="checkbox" 
                    id="item-{{ $item['name'] }}-{{ $item['label'] }}"
                    wire:model.debounce.150ms="selectedItems"
                    value="{{ $item['name'] }}" 
                    class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 focus:ring-2"
                >
                <label for="item-{{ $item['name'] }}-{{ $item['label'] }}" class="text-sm text-gray-700 cursor-pointer select-none flex-1">
                        {{ $item['label'] ?? $item['name'] }}
                </label>
            </div>
        @endforeach
    </div>
</div>
