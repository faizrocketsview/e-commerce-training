<div>
    <div class="bg-white shadow">
        <div class="px-4 max-w-7xl mx-auto sm:px-6 lg:px-8 relative">
            <div class="py-6 md:flex md:items-center md:justify-between lg:border-t lg:border-gray-200">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold leading-7 text-gray-900 capitalize sm:leading-9 sm:truncate">
                            {{ ucwords(__($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$module)) }}
                        </h1>
                    </div>
                    <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap">
                        <div class="text-sm text-gray-500 font-medium sm:mr-6">
                            {{ __($moduleSection.'/'.$moduleGroup.'/'.$module.'.module_description') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto mt-8 px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="align-middle min-w-full shadow rounded-lg">
            <nav class="h-16 hidden sm:flex bg-white rounded-t-lg px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
                aria-label="Pagination">
                
                <!-- Showing details -->
                <div>
                    <p class="text-sm text-gray-700 mt-2 mb-2">
                        @if ($items->firstItem())
                        {{ __('main.showing results', ['firstItem' => $items->firstItem(), 'lastItem' => $items->lastItem(), 'total' => number_format($items->total())]) }}
                        @endif
                    </p>
                </div>
            </nav>
            
            <!-- Data table -->
            @php
                $actionRowSelectable = false;
            @endphp
            
            <x-table>
                <x-slot name="head">
                    <x-table.header class="{{ $actionRowSelectable?'pl-6 pr-3':'pl-3' }} w-0 rounded-tl-lg sm:rounded-none">
                        @if ($actionRowSelectable)
                        <input wire:model="selectPage" type="checkbox" class="focus:ring-blue-200 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        @endif
                    </x-table.header>

                    @foreach ($indexSelectItems as $indexSelectItem)
                    @unless($indexSelectItem->hide)
                    <x-table.header class="px-3 {{ ($indexSelectItem->display) ? 'hidden '.$indexSelectItem->display.':table-cell' : null}}" :align="$indexSelectItem->align">
                        {{ __($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$indexSelectItem->label) }}
                    </x-table.header>
                    @endif
                    @endforeach
                    <x-table.header class="rounded-tr-lg sm:rounded-none" />
                </x-slot>
                <x-slot name="body">
                    @forelse($items as $item)
                    <x-table.row wire:sortable.item="{{ $item->id }}">
                        <x-table.cell class="pl-3 w-0">
                            
                        </x-table.cell>

                        @foreach ($indexSelectItems as $indexSelectItem)
                        @unless($indexSelectItem->hide)
                        <x-table.cell class="px-3 {{ ($indexSelectItem->display) ? 'hidden '.$indexSelectItem->display.':table-cell' : null}} {{ ($indexSelectItem->maxWidth) ? 'max-w-0 w-full' : null}} {{ ($indexSelectItem->truncate) ? 'truncate' : null}} {{ ($indexSelectItem->align == 'right') ? 'text-right' : null}} {{ ($indexSelectItem->highlight) ? 'text-gray-900' : ' text-gray-500'}}  {{ ($indexSelectItem->wrap) ? ' whitespace-normal' : ' whitespace-nowrap'}} {{ (isset($itemActionRowClickable)) ? ' cursor-pointer' : ''}}" wire:click="{{ (isset($itemActionRowClickable)) ? ((isset($itemActionRowClickableCustom)) ? 'custom' : Str::camel($itemActionRowClickableName) .'('. $item->id . (($itemActionRowClickableCustom) ? ',\''.$itemActionRowClickableCustom.'\'' : '') .')') : '' }}">
                            
                            @if($indexSelectItem->localize)
                            
                                @if($indexSelectItem->with)
                                {{ isset($item->{$indexSelectItem->with}->{$indexSelectItem->reference}) ? __($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$item->{$indexSelectItem->with}->{$indexSelectItem->reference}) : '-' }}
                                @else
                                {{ isset($item->{$indexSelectItem->name}) ? __($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$item->{$indexSelectItem->name}) : '-' }}
                                @endif
                            @else
                            @php
                             
                            @endphp
                                @if($indexSelectItem->with)
                                {{ isset($item->{$indexSelectItem->with}->{$indexSelectItem->reference}) ? $item->{$indexSelectItem->with}->{$indexSelectItem->reference} : '-' }}
                                @else
                                {{ isset($item->{$indexSelectItem->name}) ? $item->{$indexSelectItem->name} : '-' }}
                                @endif
                            @endif
                        </x-table.cell>
                        @endif
                        @endforeach

                        <x-table.cell class="pl-3 pr-6">
                            <div class="relative flex justify-end items-center">
                                <button type="button"
                                    class="w-5 h-5 bg-white inline-flex items-center justify-center text-gray-400 rounded-full">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                    </svg>
                                      
                                </button>
                            </div>
                              
                        </x-table.cell>
                        
                       
                        
                        
                    </x-table.row>
                    @empty
                    <x-table.row wire:loading.class.delay="opacity-50">
                        <x-table.cell class="pl-6" colspan="{{ count($indexSelectItems) + 2 }}">
                            {{ __('main.no items were found') }}
                        </x-table.cell>
                    </x-table.row>
                    @endforelse
                </x-slot>
            </x-table>

            <!-- Pagination -->
            {{ $items->links() }}
        </div>

        <div class="flex justify-end space-x-3">
            <x-button.primary type="button" wire:click="saveReorder()">{{ __('main.save') }}</x-button.primary>
            <x-button.secondary wire:click="$set('type', 'index')">{{ __('main.cancel') }}</x-button.secondary>
        </div>
    </div>
</div>