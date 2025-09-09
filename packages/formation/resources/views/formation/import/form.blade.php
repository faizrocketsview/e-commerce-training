<div>
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
                            @if($importType == 'import')
                                {{ ucwords(__('main.import')) }}
                            @elseif($importType == 'bulkEdit')
                                {{ ucwords(__('main.bulk edit')) }}
                            @endif
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto mt-8 px-4 sm:px-6 lg:px-8">
        <form wire:submit.prevent="save()" enctype="multipart/form-data">
            <div class="space-y-6 lg:col-span-9">
                @foreach($form->items as $tab)
                @foreach($tab->items as $card)
                <div class="shadow sm:rounded-md space-y-6 bg-white pt-6 pb-8 px-4 sm:px-6">
                @foreach($card->items as $section)
                
                    @if ($loop->first)
                    <div>
                        <h2 class="text-lg leading-6 font-medium text-gray-900">
                            {{ ucwords(__('main.details')) }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $card->description }}
                        </p>
                    </div>
                    @endif

                    @if($section->name)
                    <h2 class="leading-6 font-medium text-gray-900">
                        {{ $section->name }}
                    </h2>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @if($type == 'show')
                            @foreach($section->items as $column)
                            <div class="space-y-6 col-span-1 sm:col-span-{{ $column->span }}">
                                
                                @if($column->name)
                                <h2 class="leading-6 font-medium text-gray-900">
                                    {{ $column->name }}
                                </h2>
                                @endif

                                <div class="grid grid-cols-1 sm:grid-cols-{{ $column->column }} gap-6">

                                    @foreach($column->items as $field)

                                    @if ($field->type === 'preset')
                                        @continue
                                    @endif
                                    
                                    <x-input.group type="{{ $type }}" span="{{ $field->span }}" for="editing.{{ $field->name }}" label="{{ $field->label }}" help="{{ $field->help }}" description="{{ $field->description }}" required="{{ $field->required }}" hide="{{ $field->hide }}" moduleSection="{{ $moduleSection }}" moduleGroup="{{ $moduleGroup }}" module="{{ $module }}" :error="($field->addable?$errors->first('editing.'.$field->name.'*'):$errors->first('editing.'.$field->name))">

                                        @if($field->type == 'displayText')
                                            @if ($field->type === 'subfieldBox')
                                                @include('components.input.subfield-box')                                        
                                            @else
                                                @php
                                                if ($field->with) {
                                                    $tempItem = $editing;
                                                    $withItems = explode('.', $field->with);
                
                                                    foreach($withItems as $withItem) {
                                                        $tempItem = $tempItem->$withItem;
                                                        if (!isset($tempItem)) break;
                                                    }
                
                                                    $value = $tempItem->{$field->reference} ?? null;
                                                }
                                                else {
                                                    $value = $editing->{$field->name} ?? null;
                                                }
                                                @endphp

                                                <div class="flex items-center mt-1">
                                                @if ($field->href)
                                                <a href="{{ $field->href }}?type=show&formId={{ $editing->{ $field->name } }}" target="_blank" class="flex items-center">
                                                @endif

                                                <span x-ref="copy_{{ $field->name }}" class="text-sm text-gray-900 {{ $field->href?'hover:text-indigo-500':'' }}">
                                                @if ($field->localize)
                                                    {{ $value ? __($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$value) : '-' }}
                                                @else
                                                    {{ $value ?? '-' }}
                                                @endif
                                                </span>

                                            </div>
                                            @endif
                                        @endif

                                    </x-input.group>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        @endif

                        @if($type == 'create')
                            <div class="space-y-6 col-span-1 sm:col-span-1">
                                <div class="grid grid-cols-1 sm:grid-cols-1">
                                    <label for="import.attachment" class="flex item-center">
                                        <span class="mr-1 text-sm font-medium text-gray-700">{{ ucfirst(__('main.attachment')) }}</span>
                                        <span class="mr-1 text-sm font-medium text-red-500">*</span>
                                    </label>
        
                                    @foreach($form->items as $tab)
                                    @foreach($tab->items as $card)
                                    @foreach($card->items as $section)
                                    @foreach($section->items as $column)
                                    @foreach($column->items as $field)

                                    @switch($field->type)
                                    @case('file')
                                        <livewire:file-uploader :name="$field->name" :required="$field->required ?? false" disabled="{{ $field->disabled }}" :maximumFile="$field->maximumFile ?? '1'" :rules="$field->rules" :importType="$this->importType" :model="$field->model" :files="$editing->{$field->name}" :folderPath="$field->folderPath ?? '/'" :sampleFile="$field->sampleFile" :headers="collect($this->getImportAttributes()->items)->pluck('name')" :wire:key="$field->name"/>
                                        @break
                                    
                                    @endswitch

                                    @endforeach
                                    @endforeach
                                    @endforeach
                                    @endforeach
                                    @endforeach

                                    @if ($errors->first('attachment'))
                                    <div class="mt-1 text-red-500 text-sm">{{ $errors->first('attachment') }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    @if (!$loop->last)
                    <div>
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    @endif
                @endforeach
                </div>
                @endforeach
                @endforeach
                
                @if($importColumns)
                <div class="shadow sm:rounded-md space-y-6 bg-white pt-6 pb-8 px-4 sm:px-6">
                    <div>
                        <h2 class="text-lg leading-6 font-medium text-gray-900">
                            {{ ucwords(__('main.data mapping details')) }}
                        </h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach($importColumns as $field)
                            <div class="space-y-6 col-span-1 sm:col-span-1">
                                <div class="grid grid-cols-1 sm:grid-cols-1">
                                    <label for="import.{{ $field }}" class="flex item-center">
                                        <span class="mr-1 text-sm font-medium text-gray-700">{{ $field }}</span>
                                    </label>
            
                                    <x-input.select source="fieldColumnMap" lazy="" debounce="" name="{{ $field }}" prepend="" append="" placeholder="" disabled="" autofocus="">
                                        <option value="">{{ ucfirst(__('main.select column')) }}</option>
                                        @foreach($columns as $column)
                                        <option value="{{ $column }}">{{ $column }}</option>
                                        @endforeach
                                    </x-input.select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="flex justify-between items-center">
                    <div class="flex space-x-3">
                        @if($type == 'show')
                            @if($form->navigateRecord === true)
                                <x-button.secondary type="button" wire:click="previous()" :disabled="$this->isFirstRecord">{{ __('main.previous') }}</x-button.secondary>
                                <x-button.secondary type="button" wire:click="next()" :disabled="$this->isLastRecord">{{ __('main.next') }}</x-button.secondary>
                            @endif
                        @endif
                    </div>

                    <div class="flex space-x-3">
                        @if($type == 'create' || $type == 'edit')
                            <x-button.primary type="submit">{{ __('main.save') }}</x-button.primary>
                        @endif

                        @if ($form->redirectView != 'create' && $form->redirectView != 'edit')
                            <x-button.secondary type="button" wire:click="cancel()">{{ __('main.cancel') }}</x-button.secondary>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>