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

            @if (count($form->items) > 1)
            <div>
                <nav class="-mb-px flex flex-wrap" aria-label="Tabs">
                    @foreach($form->items as $tab)
                    <button wire:key="{{ Str::random(40) }}"
                        @if ($type == 'edit')
                        wire:click="edit({{ $formId }}, {{ $loop->iteration }})"
                        @elseif ($type == 'show')
                        wire:click="show({{ $formId }}, {{ $loop->iteration }})"
                        @endif
                        class="mr-8 {{ ($loop->iteration == $selectedTab)? 'border-indigo-500 text-indigo-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-200' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none {{ ($type == 'edit' || $type == 'show') ? '' : 'cursor-default' }}">
                        {{ __($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$tab->name) }}
                    </button>
                    @endforeach
                </nav>
            </div>
            @endif
        </div>
    </div>
    <div class="max-w-7xl mx-auto mt-8 px-4 sm:px-6 lg:px-8">
        <form wire:submit.prevent="save()">
            <div class="space-y-6 lg:col-span-9">
                @foreach($form->items as $tab)
                @foreach($tab->items as $card)
                <div class="shadow sm:rounded-md space-y-6 bg-white pt-6 pb-8 px-4 sm:px-6">
                @foreach($card->items as $section)

                    @if ($loop->first)
                    <div>
                        <h2 class="text-lg leading-6 font-medium text-gray-900">
                            {{ __($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$card->name) }}
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

                    <div class="grid grid-cols-1 sm:grid-cols-{{ $section->column }} gap-6">

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

                                @php
                                    $tempItem = null;

                                    if ($field->with) {
                                        $tempItem = $editing;
                                        $withItems = explode('.', $field->with);
    
                                        foreach($withItems as $withItem) {
                                            $tempItem = $tempItem->$withItem;
                                            if (!isset($tempItem)) break;
                                        }
                                    }
                                    
                                    $isModelTranslatable = $this->isModelTranslatable($tempItem ?? $editing, $field->reference ?? $field->name);
                                @endphp

                                <x-input.group type="{{ $type }}" span="{{ $field->span }}" for="editing.{{ $field->name }}" label="{{ $field->label }}" help="{{ $field->help }}" description="{{ $field->description }}" required="{{ $field->required }}" hide="{{ $field->hide }}" moduleSection="{{ $moduleSection }}" moduleGroup="{{ $moduleGroup }}" module="{{ $module }}" :lang="$isModelTranslatable ? ($field->lang ?? \App::getFallbackLocale()) : null" :error="($field->addable?$errors->first('editing.'.$field->name.'*'): $errors->first('editing.'.$field->name. ($isModelTranslatable ? '.' . ($field->lang ?? config('app.fallback_locale')) : '*')))">

                                    @if($type == 'show' || (($type == 'create' || $type == 'edit') && $field->type == 'displayText'))
                                        @if ($field->type === 'subfieldBox')
                                            @include('components.input.subfield-box')
                                        @else
                                            @php
                                            if ($field->with) {
                                                if($isModelTranslatable) {
                                                    $langToUse = $field->lang ?? config('app.fallback_locale', 'en');
                                                    $value = $tempItem->getTranslation($field->reference, $langToUse) ?? null;
                                                } else {
                                                    $value = $tempItem->{$field->reference} ?? null;
                                                }
                                            }
                                            else {
                                                if($isModelTranslatable) {
                                                    $langToUse = $field->lang ?? config('app.fallback_locale', 'en');
                                                    $value = $editing->getTranslation($field->name, $langToUse) ?? null;
                                                } else {
                                                    $value = $editing->{$field->name} ?? null;
                                                }
                                            }
                                            @endphp

                                            @switch($field->type)
                                            @case('file')
                                                <livewire:file-uploader :name="$field->name" :required="$field->required ?? false" disabled=true :maximumFile="$field->maximumFile ?? '1'" :rules="$field->rules" :importType="$this->importType" :model="$field->model" :files="$editing->{$field->name}" :folderPath="$field->folderPath ?? '/'" :sampleFile="$field->sampleFile" :headers="collect($column->items)->pluck('name')" :fields="$field" :wire:key="$field->name"/>
                                                @break
                                            @case('richText')
                                                <x-input.rich-text source="editing" lazy="{{ $field->lazy }}" debounce="{{ $field->debounce }}" name="{{ $field->name }}" id="editing.{{ $field->name }}" placeholder="{{ $field->placeholder }}" readonly="{{ $field->readonly }}" disabled="{{ $field->disabled }}" autofocus="{{ $field->autofocus }}" height="{{ $field->height }}" value="{{ $editing->{$field->name} }}" type="{{ $type }}"/>
                                                @break;
                                            @default
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

                                                    @if ($field->href)
                                                    <svg class="ml-2 w-4 h-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                                    </svg>
                                                    </a>
                                                    @endif

                                                    @if ($field->copy)
                                                    <div class="flex relative items-center" x-data="{ open: false }" @showbox="setTimeout(() => open = false, 2000)">
                                                        <button class="ml-2" type="button" x-on:click="open = ! open;navigator.clipboard.writeText($refs.copy_{{ $field->name }}.innerHTML.trim());$dispatch('showbox')">
                                                        <svg class="w-4 h-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                                        </svg>
                                                        </button>
                                                        <template x-if="open" x-transition>
                                                            <div class="absolute left-7 flex items-center">
                                                                <div class="w-4 h-4 bg-gray-900 rotate-45 transform origin-center rounded-sm"></div>
                                                                <span class="relative -left-3.5 rounded-md bg-gray-900 px-2 py-1 text-xs font-medium text-white">{{ __('main.copied') }}</span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                    @endif
                                                </div>
                                                @break
                                            @endswitch
                                        @endif


                                    @elseif ($type == 'create' || $type == 'edit')
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
                                            <x-input.text source="editing" lazy="{{ $field->lazy }}" debounce="{{ $field->debounce }}" name="{{ $field->name }}" type="{{ $field->type != 'phoneNumber' ? $field->type : 'tel' }}" prepend="{{ $field->prepend }}" append="{{ $field->append }}" placeholder="{{ $field->placeholder }}" readonly="{{ $field->readonly }}" disabled="{{ $field->disabled }}" autofocus="{{ $field->autofocus }}" addable="{{ $field->addable }}" count="{{ ($editing->{$field->name} && $field->addable)?count($editing->{$field->name}):1 }}" :lang="$isModelTranslatable ? ($field->lang ?? config('app.fallback_locale')) : null"/>
                                            @break

                                        @case('textarea')
                                            <x-input.textarea source="editing" lazy="{{ $field->lazy }}" debounce="{{ $field->debounce }}" name="{{ $field->name }}" placeholder="{{ $field->placeholder }}" readonly="{{ $field->readonly }}" disabled="{{ $field->disabled }}" autofocus="{{ $field->autofocus }}" height="{{ $field->height }}" :lang="$isModelTranslatable ? ($field->lang ?? config('app.fallback_locale')) : null"/>
                                            @break

                                        @case('richText')
                                            <x-input.rich-text source="editing" lazy="{{ $field->lazy }}" debounce="{{ $field->debounce }}" name="{{ $field->name }}" id="editing.{{ $field->name }}" placeholder="{{ $field->placeholder }}" readonly="{{ $field->readonly }}" disabled="{{ $field->disabled }}" autofocus="{{ $field->autofocus }}" height="{{ $field->height }}" type="{{ $type }}"/>
                                            @break

                                        @case('select')
                                            <x-input.select source="editing" lazy="{{ $field->lazy }}" debounce="{{ $field->debounce }}" name="{{ $field->name }}" prepend="{{ $field->prepend }}" append="{{ $field->append }}" placeholder="{{ $field->placeholder }}" disabled="{{ $field->disabled }}" autofocus="{{ $field->autofocus }}">

                                                @foreach($field->options as $option)
                                                <option value="{{ $option->name }}">{{ $option->label }}</option>
                                                @endforeach
                                            </x-input.select>
                                            @break

                                        @case('radio')
                                            <x-input.radio-group flex="{{ $field->flex }}" id="{{ $field->name }}">

                                                @foreach($field->options as $option)
                                                <x-input.radio source="editing" lazy="{{ $field->lazy }}" debounce="{{ $field->debounce }}" name="{{ $field->name }}" option="{{ $option->name }}" disabled="{{ $field->disabled }}" value="{{ $option->name }}"  autofocus="{{ $field->autofocus }}">{{ $option->label }}</x-input.radio>
                                                @endforeach
                                            </x-input.radio-group>
                                            @break

                                        @case('radioButton')
                                        @case('checkbox')
                                            <x-input.radio-group flex="{{ $field->flex }}" id="editing.{{ $field->name }}">
                                                <x-input.checkbox source="editing" lazy="{{ $field->lazy }}" debounce="{{ $field->debounce }}" name="{{ $field->name }}" option="{{ $field->options[0]->name }}" disabled="{{ $field->disabled }}" value="{{ $field->options[0]->name }}"  autofocus="{{ $field->autofocus }}">{{ $field->options[0]->label }}</x-input.checkbox>
                                            </x-input.radio-group>
                                            @break

                                        @case('checkboxButton')
                                        @case('checkboxButtonMultiple')
                                            <x-input.radio-group flex="{{ $field->flex }}" id="editing.{{ $field->name }}">
                                                @if($field->selectAll)
                                                    <livewire:select-all-checkbox-multiple
                                                        :name="$field->name"
                                                        :source="'editing'"
                                                        :items="$field->options"
                                                        :selectedItems="is_array($editing->{$field->name}) && sizeof($editing->{$field->name}) ? array_column(array_filter($field->options, fn($item) => in_array($item->name, $editing->{$field->name})), 'name') : []"
                                                        :wire:key="'multi-checkbox-'.$field->name.'-'.($field->lazy ? time() : ($this->formId ?? 0))"
                                                    />
                                                @else
                                                @foreach($field->options as $option)
                                                    <x-input.checkbox-multiple source="editing" lazy="{{ $field->lazy }}" debounce="{{ $field->debounce }}" name="{{ $field->name }}" option="{{ $option->name }}" disabled="{{ $field->disabled }}" value="{{ $option->name }}"  autofocus="{{ $field->autofocus }}">{{ $option->label }}</x-input.checkbox-multiple>
                                                    @endforeach
                                                @endif
                                            </x-input.radio-group>
                                            @break

                                        @case('checkboxButtonMultiple')
                                        @case('file')
                                            <livewire:file-uploader :name="$field->name" :required="$field->required ?? false" disabled="{{ $field->disabled }}" :maximumFile="$field->maximumFile ?? '1'" :rules="$field->rules" :importType="$this->importType" :model="$field->model" :files="$editing->{$field->name}" :folderPath="$field->folderPath ?? '/'" :sampleFile="$field->sampleFile" :headers="collect($column->items)->pluck('name')" :wire:key="$field->name"/>
                                            @break

                                        @case('checkboxMultiple')
                                            <x-input.radio-group flex="{{ $field->flex }}" id="editing.{{ $field->name }}">
                                                @foreach($field->options as $option)
                                                    <x-input.checkbox-multiple source="editing" lazy="{{ $field->lazy }}" debounce="{{ $field->debounce }}" name="{{ $field->name }}" option="{{ $option->name }}" disabled="{{ $field->disabled }}" value="{{ $option->name }}" autofocus="{{ $field->autofocus }}">{{ $option->label }}</x-input.checkbox-multiple>
                                                @endforeach
                                            </x-input.radio-group>
                                            @break

                                        @case('coordinate')
                                            @break

                                        @case('subfieldBox')
                                            @include('components.input.subfield-box')
                                            @break;

                                        @default
                                            @include('components.input.'.$field->type)

                                    @endswitch
                                    @endif

                                </x-input.group>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
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
        
        <!-- Preview Image Modal -->
        <x-modal.image-preview wire:model.defer="showPreviewImageModal">
            <x-slot name="content">
                @if(isset($previewImageUrl))
                    <img class="w-full rounded-lg" src="{{ $previewImageUrl }}">
                @endif
            </x-slot>
            <x-slot name="footer">
                <x-button.secondary type="button" wire:click="$set('showPreviewImageModal', false)">{{ __('main.close') }}</x-button.secondary>
            </x-slot>
        </x-modal.image-preview>
    </div>
</div>
