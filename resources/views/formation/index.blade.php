<div {{ $poll?'wire:poll.'.$poll:'' }}>
    <div class="bg-white shadow">
        <div class="px-4 max-w-7xl mx-auto sm:px-6 lg:px-8 relative">

            <!-- Notification -->
            <div x-data="{
                    messages: [],
                    remove(message) {
                        this.messages.splice(this.messages.map((message) => message.message).indexOf(message), 1)
                    },
                }"
                @notify.window="let type = $event.detail.type; let message = $event.detail.message; let description = $event.detail.description; messages.push({'message': message, 'type': type, 'description': description}); setTimeout(() => { remove(message) }, 5000)"
                class="z-10 origin-top-right fixed top-0 right-0 flex w-full flex-col items-center space-y-4 sm:items-end sm:px-6">
                <template x-for="(message, messageIndex) in messages" :key="messageIndex">
                    <div class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5" :class="message.type == 'error' ? 'border-l-8 border-red-500' : 'border-l-8 border-green-400'">
                        <div class="p-4">
                            <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg 
                                    x-show="message.type == 'success'"
                                    x-cloak 
                                    class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg 
                                    x-show="message.type == 'error'"
                                    x-cloak 
                                    class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p x-text="message.message" class="text-sm font-medium text-gray-900"></p>
                                <p x-text="message.description" class="mt-1 text-sm text-gray-500"></p>
                            </div>
                            <div class="ml-4 flex flex-shrink-0">
                                <button @click="remove(message.message)" type="button" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:ring-offset-2">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                </svg>
                                </button>
                            </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

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
    
            <div>
                <nav class="-mb-px flex flex-wrap" aria-label="Tabs">

                    @foreach ($indexTabItems as $indexTab)

                    @php
                        foreach ($indexTab->filter as $key => $value)
                        {
                            $indexTabHighlight = true;
                            if ($filters[$key] != $value)
                            {
                                $indexTabHighlight = false;
                                break;
                            }
                        }
                    @endphp

                    <button wire:click="tab({ @foreach ($indexTab->filter as $key => $value) {{ $key }}: '{{ $value }}', @endforeach })" 
                        class="mr-8 {{ ($indexTabHighlight)? 'border-indigo-500 text-indigo-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-200' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none">
                        {{ $indexTab->name }}
                        @if ($indexTab->badge)
                        <span class="{{ ($indexTabHighlight)? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800' }} ml-2 rounded-full py-0.5 px-2.5 text-xs font-medium">{{ $indexTab->badge }}</span>
                        @endif
                    </button>
                    @endforeach
                </nav>
            </div>

        </div>
    </div>
    <div class="max-w-7xl mx-auto mt-8 px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="mt-6 flex justify-between">
            
            <!-- Search & Filter -->
            <div class="flex space-x-3 md:mt-0">
                @if ($searchItems)
                <div class="w-full">
                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-4 w-4 text-gray-400"
                                x-description="Heroicon name: solid/search"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input wire:model="search" id="search" name="search"
                            class="block w-full bg-white border border-gray-300 rounded-md py-2 pl-10 pr-3 text-sm placeholder-gray-500 focus:outline-none focus:text-gray-900 focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-200 focus:border-blue-200 text-sm"
                            placeholder="Search" type="search">
                    </div>
                </div>
                @endif

                @if ($filterItems)
                <x-button.primary wire:click="toggleShowFilter">{{ __('main.filter') }}</x-button.primary>
                @endif

            </div>
            
            @if ($actionItems)
            <!-- Bulk action -->
            <div x-data="{ isActionOpen: false }" @keydown.escape.stop="isActionOpen = false"
                @click.away="isActionOpen = false" class="ml-3 relative inline-block text-left">
                <div>
                    <button @click="isActionOpen = !isActionOpen" type="button"
                        class="p-2 rounded-full bg-white flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-200"
                        id="menu-4" aria-expanded="false" aria-haspopup="true">
                        <!-- Heroicon name: solid/dots-vertical -->
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                        </svg>
                    </button>
                </div>

                <div x-show="isActionOpen" 
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="z-10 origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-200 focus:outline-none"
                    role="menu" aria-orientation="vertical" aria-labelledby="menu-4" style="display: none;">
                    <div class="py-1" role="none">
                        
                        @foreach($actionItems as $action)
                        
                        @if ($action->break)
                    </div>
                    <div class="py-1" role="none">
                        @endif

                        @php
                            switch ($action->name) {
                                case 'create':
                                case 'import':
                                    $actionPermission = $moduleSection.'.'.$moduleGroup.'.'.$module.':create';
                                    break;
                                case 'bulkEdit':
                                case 'reorder':
                                    $actionPermission = $moduleSection.'.'.$moduleGroup.'.'.$module.':edit';
                                    break;
                                case 'export':
                                    $actionPermission = $moduleSection.'.'.$moduleGroup.'.'.$module.':show';
                                    break;
                                case 'bulkDelete':
                                    $actionPermission = $moduleSection.'.'.$moduleGroup.'.'.$module.':delete';
                                    break;
                                default:
                                    $actionPermission = null;
                                    break;
                            }
                        @endphp

                        @if ($actionPermission)
                            @can($actionPermission)
                                <button wire:click="{{ $action->name }}"
                                    class="w-full text-left px-4 py-2 text-sm {{ ($action->danger)?'text-red-600':'text-gray-700' }} capitalize hover:bg-gray-100 hover:{{ ($action->danger)?'text-red-600':'text-gray-900' }} focus:outline-none"
                                    role="menuitem">
                                    {{ __('main.'.Str::snake($action->name, ' ')) }}
                                </button>
                            @endcan
                        @endif

                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        @if ($filterItems)
        @if ($showFilter)
        <!-- Filter -->
        <div class="shadow sm:rounded-md sm:overflow-hidden">
            <div class="bg-white py-6 px-4 sm:p-6">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                    @foreach($filterItems as $field)

                    @if ($field->type === 'preset')
                        @continue
                    @endif

                    <x-input.group span="{{ $field->span }}" for="filters.{{ ($field->with ? $field->with.'-'.$field->reference : $field->label) }}" label="{{ $field->label }}" help="{{ $field->help }}" description="{{ $field->description }}" required="{{ $field->required }}" hide="{{ $field->hide || $field->type=='hidden' }}" moduleSection="{{ $moduleSection }}" moduleGroup="{{ $moduleGroup }}" module="{{ $module }}">
                        

                        @switch($field->type)
                            @case('text')
                            @case('number')
                            @case('password')
                            @case('email')
                            @case('phoneNumber')
                            @case('url')
                            @case('date')
                            @case('time')
                            @case('month')
                            @case('week')
                            @case('color')
                            @case('range')
                            @case('hidden')
                                <x-input.text source="filters" lazy="{{ $field->lazy }}" debounce="{{ $field->debounce }}" name="{{ ($field->with ? $field->with.'-'.$field->reference : $field->label) }}" type="{{ $field->type != 'phoneNumber' ? $field->type : 'tel' }}" prepend="{{ $field->prepend }}" append="{{ $field->append }}" placeholder="{{ $field->placeholder }}" readonly="{{ $field->readonly }}" disabled="{{ $field->disabled }}" autofocus="{{ $field->autofocus }}" />
                                @break

                            @case('textarea')
                                <x-input.textarea source="filters" lazy="{{ $field->lazy }}" debounce="{{ $field->debounce }}" name="{{ ($field->with ? $field->with.'-'.$field->reference : $field->label) }}" placeholder="{{ $field->placeholder }}" readonly="{{ $field->readonly }}" disabled="{{ $field->disabled }}" autofocus="{{ $field->autofocus }}" height="{{ $field->height }}" />
                                @break

                            @case('richText')
                                <x-input.rich-text wire:model="filters.{{ ($field->with ? $field->with.'-'.$field->reference : $field->label) }}" id="filters.{{ ($field->with ? $field->with.'-'.$field->reference : $field->label) }}" placeholder="{{ $field->placeholder }}" readonly="{{ $field->readonly }}" disabled="{{ $field->disabled }}" autofocus="{{ $field->autofocus }}" height="{{ $field->height }}" />
                                @break

                            @case('select')
                                <x-input.select source="filters" lazy="{{ $field->lazy }}" debounce="{{ $field->debounce }}" name="{{ ($field->with ? $field->with.'-'.$field->reference : $field->label) }}" prepend="{{ $field->prepend }}" append="{{ $field->append }}" placeholder="{{ $field->placeholder }}" disabled="{{ $field->disabled }}" autofocus="{{ $field->autofocus }}">
                                    
                                    @foreach($field->options as $option)
                                    <option value="{{ $option->name }}">{{ $option->label }}</option>
                                    @endforeach
                                </x-input.select>
                                @break
                            
                            @case('radio')
                                <x-input.radio-group flex="{{ $field->flex }}" id="{{ $field->label }}">

                                    @foreach($field->options as $option)
                                    <x-input.radio source="filters" lazy="{{ $field->lazy }}" debounce="{{ $field->debounce }}" name="{{ ($field->with ? $field->with.'-'.$field->reference : $field->label) }}" option="{{ $option->name }}" disabled="{{ $field->disabled }}" value="{{ $option->name }}"  autofocus="{{ $field->autofocus }}">{{ $option->label }}</x-input.radio>
                                    @endforeach
                                </x-input.radio-group>
                                @break

                            @case('radioButton')
                            @case('checkbox')
                                <x-input.radio-group flex="{{ $field->flex }}" id="{{ $field->label }}">
                                    <x-input.checkbox source="filters" lazy="{{ $field->lazy }}" debounce="{{ $field->debounce }}" name="{{ ($field->with ? $field->with.'-'.$field->reference : $field->label) }}" disabled="{{ $field->disabled }}" value="{{ $field->options[0]->name }}"  autofocus="{{ $field->autofocus }}">{{ $field->options[0]->label }}</x-input.checkbox>
                                </x-input.radio-group>
                                @break

                            @case('checkboxButton')
                            @case('checkboxMultiple')
                                <x-input.radio-group flex="{{ $field->flex }}" id="{{ $field->label }}">

                                    @foreach($field->options as $option)
                                    <x-input.checkbox wire:model="filters.{{ $field->label.'.'.$option->name }}" id="filters.{{ $field->label.'_'.$option->name }}" disabled="{{ $field->disabled }}" value="{{ $option->name }}"  autofocus="{{ $field->autofocus }}">{{ $option->label }}</x-input.checkbox>
                                    @endforeach
                                </x-input.radio-group>
                                @break 
                                
                            @case('checkboxButtonMultiple')
                            @case('file')
                            @case('image')
                            @case('coordinate')

                        
                            @default
                                
                        @endswitch

                    </x-input.group>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end px-4 py-3 bg-gray-50 space-x-2 sm:px-6">
                <x-button.primary wire:click="resetFilter">{{ __('main.reset') }}</x-button.primary>
                <x-button.secondary wire:click="toggleShowFilter">{{ __('main.close') }}</x-button.secondary>
            </div>
        </div>
        @endif
        @endif
        
        <div class="align-middle min-w-full shadow rounded-lg">
            <nav class="hidden sm:flex bg-white rounded-t-lg px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
                aria-label="Pagination">
                
                <!-- Showing details -->
                <div>
                    <p class="text-sm text-gray-700">
                        @if ($items->firstItem())
                            {{ trans_choice('main.showing results', $paginateType == 'simple' ? 0 : number_format($items->total()), ['firstItem' => $items->firstItem(), 'lastItem' => $items->lastItem(), 'total' => $paginateType == 'simple' ? 0 : number_format($items->total())]); }}
                        @endif
                    </p>
                </div>
                
                <!-- Items per page -->
                <div class="flex items-center">
                    <label for="email" class="inline-block align-middle text-sm font-medium text-gray-700">
                        {{ __('main.results per page') }}
                    </label>

                    <select wire:model="perPage" class="ml-3 pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-blue-200 focus:border-blue-200 text-sm rounded-md">
                        <option>5</option>
                        <option>10</option>
                        <option>20</option>
                        <option>50</option>
                        <option>100</option>
                        <option>200</option>
                    </select>
                </div>
            </nav>
            
            <!-- Data table -->
            @if ($itemActionItems)
            @foreach ($itemActionItems as $itemAction)
                @if ($itemAction->rowClickable)
                @php
                    $itemActionRowClickable = true;
                    $itemActionRowClickableCustom = $itemAction->custom;
                    $itemActionRowClickableItemType = $itemAction->itemType;
                    $itemActionRowClickableName = $itemAction->name;
                @endphp
                @endif
            @endforeach
            @endif


            @php
                $actionRowSelectable = false;
            @endphp
            @if ($actionItems)
            @foreach ($actionItems as $action)
                @if ($action->name === 'bulkDelete')
                @php
                    $actionRowSelectable = true;
                @endphp
                @endif
            @endforeach
            @endif
            
            <x-table>
                <x-slot name="head">
                    <x-table.header class="{{ $actionRowSelectable?'pl-6 pr-3':'pl-3' }} w-0 rounded-tl-lg sm:rounded-none">
                        @if ($actionRowSelectable)
                        <input wire:model="selectPage" type="checkbox" class="focus:ring-blue-200 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        @endif
                    </x-table.header>

                    @foreach ($indexSelectItems as $indexSelectItem)
                    @unless($indexSelectItem->hide)
                    <x-table.header wire:click="sortBy('{{ $indexSelectItem->name }}')" class="px-3 {{ ($indexSelectItem->display) ? 'hidden '.$indexSelectItem->display.':table-cell' : null}}" :align="$indexSelectItem->align" :sortable="$indexSelectItem->sortable" :direction="$sorts[$indexSelectItem->name] ?? null">
                        {{ __($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$indexSelectItem->label) }}
                    </x-table.header>
                    @endif
                    @endforeach
                    <x-table.header class="rounded-tr-lg sm:rounded-none" />
                </x-slot>
                <x-slot name="body">
                    @if ($selectPage)
                    <x-table.row wire:loading.class.delay="opacity-50" wire:key="row-message">
                        <x-table.cell class="px-3 pl-6" colspan="{{ count($indexSelectItems) + 2 }}">
                            @if ($selectAll)
                            {!! __('main.all items are selected', ['total' => $items->total()]) !!}
                            @else
                            {!! __('main.all items on this page are selected', ['count' => $items->count()]) !!}
                            <button wire:click="selectAll" class="text-indigo-600 focus:outline-none">
                                {!! __('main.select all items', ['total' => $items->total()]) !!}
                            </button>
                            @endif
                            
                        </x-table.cell>
                    </x-table.row>
                    @endif
                    @forelse($items as $item)
                    <x-table.row wire:loading.class.delay="opacity-50" wire:key="row-{{ $item->id }}">
                        <x-table.cell class="{{ $actionRowSelectable?'pl-6 pr-3':'pl-3' }} w-0">
                            @if ($actionRowSelectable)
                            <input wire:model="selected" type="checkbox" value="{{ $item->id }}" class="flex focus:ring-blue-200 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            @endif
                        </x-table.cell>

                        @foreach ($indexSelectItems as $indexSelectItem)
                        @unless($indexSelectItem->hide)
                        <x-table.cell class="px-3 {{ ($indexSelectItem->display) ? 'hidden '.$indexSelectItem->display.':table-cell' : null}} {{ ($indexSelectItem->maxWidth) ? 'max-w-0 w-full' : null}} {{ ($indexSelectItem->truncate) ? 'truncate' : null}} {{ ($indexSelectItem->align == 'right') ? 'text-right' : null}} {{ ($indexSelectItem->highlight) ? 'text-gray-900' : ' text-gray-500'}}  {{ ($indexSelectItem->wrap) ? ' whitespace-normal' : ' whitespace-nowrap'}} {{ (isset($itemActionRowClickable)) ? ' cursor-pointer' : ''}}" wire:click="{{ (isset($itemActionRowClickable)) ? ((isset($itemActionRowClickableCustom) ? 'custom' : Str::camel($itemActionRowClickableName)) .'('. (($itemActionRowClickableCustom) ? '\''.$itemActionRowClickableCustom.'\',' : '') . $item->id .(($itemActionRowClickableItemType) ? ',\''.$itemActionRowClickableItemType.'\'' : '').')') : '' }}">
                            @if ($indexSelectItem->badges)
                            <span class="inline-flex items-center rounded-md bg-{{ $indexSelectItem->badges[$item->{$indexSelectItem->name}]->color??'gray' }}-100 px-2 py-1 text-sm font-medium text-{{ $indexSelectItem->badges[$item->{$indexSelectItem->name}]->color??'gray' }}-800">
                            @endif

                            @php
                                $tempItem = null;

                                if ($indexSelectItem->with) {
                                    $tempItem = $item;
                                    $withItems = explode('.', $indexSelectItem->with);

                                    foreach ($withItems as $withItem) {
                                        $tempItem = $tempItem->$withItem;

                                        if (!isset($tempItem))
                                            break;
                                    }

                                    $value = $tempItem->{$indexSelectItem->reference} ?? null;
                                }
                                else {
                                    $value = $item->{$indexSelectItem->name} ?? null;
                                }
                            @endphp
                            
                            @if(isset($tempItem) && $this->isModelTranslatable($tempItem, $indexSelectItem->reference))
                                <x-multi-language-label :moduleSection='$moduleSection' :moduleGroup='$moduleGroup' :module='$module' :translations='$tempItem->getTranslations($indexSelectItem->reference)' :localize='$indexSelectItem->localize' :allowedLangs='$indexSelectItem->lang'>
                                </x-multi-language-label>                                
                            @elseif($this->isModelTranslatable($item, $indexSelectItem->name))
                                <x-multi-language-label :moduleSection='$moduleSection' :moduleGroup='$moduleGroup' :module='$module' :translations='$item->getTranslations($indexSelectItem->name)' :localize='$indexSelectItem->localize' :allowedLangs='$indexSelectItem->lang'>
                                </x-multi-language-label>
                            @else
                                @if($indexSelectItem->localize)
                                    {{ $value ? __($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$value) : '-' }}
                                @else
                                    {{ $value ?? '-' }}
                                @endif
                            @endif

                            @if ($indexSelectItem->badges)
                            </span>
                            @endif
                        </x-table.cell>
                        @endif
                        @endforeach
                        
                       
                        <x-table.cell class="{{ $itemActionItems?'pl-3 pr-6':'pr-3' }}">
                            @if ($itemActionItems)
                            <div class="flex">
                                @foreach($itemActionItems as $itemAction)
                                @if($itemAction->iconClickable)
                                <div class="relative flex justify-end items-center">
                                    <button type="button"
                                        wire:click="{{ ($itemAction->custom) ? 'custom' : Str::camel($itemAction->name) }}({{ ($itemAction->custom) ? '\''.$itemAction->custom.'\',' : '' }}{{ $item->id }}{{ ($itemAction->itemType) ? ',\''.$itemAction->itemType.'\'' : '' }})"
                                        class="w-5 h-5 bg-white inline-flex items-center justify-center text-gray-400 rounded-full hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-200">
                                        
                                        @if($itemAction->name == 'show')
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        @elseif($itemAction->name == 'edit')
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                        @elseif($itemAction->name == 'delete')
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        @elseif(substr($itemAction->name, 0, 6) == 'manage')
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0-3.75-3.75M17.25 21 21 17.25" />
                                        </svg>
                                        @else
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
                                        </svg>                                          
                                        @endif
                                    </button>
                                </div>
                                @endif
                                @endforeach
                                <div x-data="{ open: false }" @keydown.escape.stop="open = false"
                                    @click.away="open = false"
                                    class="relative flex justify-end items-center">
                                    <button type="button"
                                        class="w-5 h-5 bg-white inline-flex items-center justify-center text-gray-400 rounded-full hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-200"
                                        @click="open = !open"
                                        aria-haspopup="true" x-bind:aria-expanded="open">
                                        <svg class="h-4 w-4"" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                    <div x-description="Dropdown menu, show/hide based on menu state."
                                        x-show="open" 
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="mx-3 origin-top-right absolute right-7 top-0 w-48 mt-1 rounded-md shadow-lg z-10 bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-200 focus:outline-none"
                                        role="menu" aria-orientation="vertical"
                                        aria-labelledby="project-options-menu-0" style="display: none;">
                                        <div class="py-1" role="none">
                                            @foreach($itemActionItems as $itemAction)
                                            @if ($itemAction->break)
                                        </div>
                                        <div class="py-1" role="none">
                                            @endif

                                            @php
                                                switch ($itemAction->name) {
                                                    case 'show':
                                                        $itemActionPermission = $moduleSection.'.'.$moduleGroup.'.'.$module.':show';
                                                        break;
                                                    case 'edit':
                                                        $itemActionPermission = $moduleSection.'.'.$moduleGroup.'.'.$module.':edit';
                                                        break;
                                                    case 'delete':
                                                        $itemActionPermission = $moduleSection.'.'.$moduleGroup.'.'.$module.':delete';
                                                        break;
                                                    default:
                                                        $itemActionPermission = null;
                                                        break;
                                                }
                                            @endphp

                                            @if ($itemActionPermission)
                                                @can($itemActionPermission)
                                                    <button wire:click="{{ ($itemAction->custom) ? 'custom' : Str::camel($itemAction->name) }}({{ ($itemAction->custom) ? '\''.$itemAction->custom.'\',' : '' }}{{ $item->id }}{{ ($itemAction->itemType) ? ',\''.$itemAction->itemType.'\'' : '' }})"
                                                        class="w-full flex items-center px-4 py-2 text-left text-sm capitalize {{ ($itemAction->danger)?'text-red-500':'text-gray-700' }} hover:bg-gray-100 hover:{{ ($itemAction->danger)?'text-red-500':'text-gray-900' }} focus:outline-none">
                                                        @if ($itemAction->custom)
                                                        {{ __($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.Str::snake($itemAction->name, ' ')) }}
                                                        @else
                                                        {{ __('main.'.Str::snake($itemAction->name, ' ')) }}
                                                        @endif
                                                    </button>
                                                @endcan
                                            @endif

                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
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

        <!-- Delete Modal -->
        <form wire:submit.prevent="destroy">
            <x-modal.confirmation wire:model.defer="showDeleteModal">
                <x-slot name="title">{{ __('main.delete items') }}</x-slot>

                <x-slot name="content">
                    <div class="py-8 text-cool-gray-700">{{ __('main.are you sure you want to delete these items') }}</div>
                </x-slot>

                <x-slot name="footer">
                    <x-button.primary type="submit">{{ __('main.delete') }}</x-button.primary>
                    <x-button.secondary type="button" wire:click="$set('showDeleteModal', false)">{{ __('main.close') }}</x-button.secondary>
                </x-slot>
            </x-modal.confirmation>
        </form>
    </div>
</div>