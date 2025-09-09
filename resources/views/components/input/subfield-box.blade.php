<div class="mt-2">
@php
    if($type == 'create' || $type == 'edit') {
        // Prefer pre-hydrated arrays from the component when available; otherwise fallback to relation
        if(isset($editedSubClassFields) 
            && isset($editedSubClassFields[$field->with]) 
            && is_array($editedSubClassFields[$field->with]) 
            && sizeof($editedSubClassFields[$field->with]) > 0){
            $subClassFields = $editedSubClassFields[$field->with];
        } else {
            $subClassFields = $editing->{$field->with};
        }
    } else {
        $subClassFields = $editing->{$field->with};
    }
@endphp

@if(isset($field->with))
@if((isset($subClassItems[$field->with]) && sizeof($subClassItems[$field->with]) > 0) || (isset($subClassFields) && sizeof($subClassFields) > 0))
@foreach($subClassFields as $key => $editing_subfield)
    @if($type == 'create' || $type == 'edit')
    <div class="text-gray-400 hover:text-gray-600 bg-gray-100 {{ $key == 0 ? 'rounded-t-lg' : '' }} px-4 sm:px-6 pt-2 sm:pt-4">
        <div class="flex flex-row-reverse">
            <svg wire:click="removeEditingSubField('{{ $field->with }}', {{ $key }})" class="w-5 h-5 cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
            </svg> 
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-{{ $field->column }} gap-6 bg-gray-100 {{ isset($subClassFields) && (sizeof($subClassFields) == $key + 1) && !isset($subClassItems[$field->with])? 'rounded-b-lg' : '' }} {{ $type == 'show' ? 'pt-8' : '' }} {{ $key == 0 ? 'rounded-t-lg' : '' }} px-4 sm:px-6 pb-10">
    @foreach($field->items as $subfield)
        @if($type == 'show' || (($type == 'create' || $type == 'edit') && $subfield->type == 'displayText'))
            @if ($subfield->type != 'preset')
                <x-input.group type="{{ $type }}" span="{{ $subfield->span }}" for="editing.{{ $subfield->name }}" label="{{ $subfield->label }}" help="{{ $subfield->help }}" description="{{ $subfield->description }}" required="{{ $subfield->required }}" hide="{{ $subfield->hide }}" moduleSection="{{ $moduleSection }}" moduleGroup="{{ $moduleGroup }}" module="{{ $module }}" :error="$errors->first('editing.' .$field->with. '.' .$key. '.' .$subfield->name)">
                    @php
                        // When editing, values may come from $editedSubClassFields (arrays) instead of Eloquent relations
                        if(isset($editedSubClassFields)) {
                            $value = $subClassFields[$key][$subfield->name] ?? null;
                        } else {
                            $value = $editing->{$field->with}[$key]->{$subfield->name} ?? null;
                        }
                    @endphp

                    <div class="flex items-center mt-1">
                        @if ($subfield->href)
                        <a href="{{ $subfield->href }}?type=show&formId={{ $value }}" target="_blank" class="flex items-center">
                        @endif

                        <span x-ref="copy_editing_{{ $field->with }}_{{ $key }}_{{ $subfield->name }}" class="text-sm text-gray-900 {{ $subfield->href?'hover:text-indigo-500':'' }}">
                        @if ($subfield->localize)
                            {{ $value ? __($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$value) : '-' }}
                        @else
                            {{ $value ?? '-' }}
                        @endif
                        </span>

                        @if ($subfield->href)
                        <svg class="ml-2 w-4 h-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                        </a>
                        @endif

                        @if ($subfield->copy)
                        <div class="flex relative items-center" x-data="{ open: false }" @showbox="setTimeout(() => open = false, 2000)">
                            <button class="ml-2" type="button" x-on:click="open = ! open;navigator.clipboard.writeText($refs.copy_editing_{{ $field->with }}_{{ $key }}_{{ $subfield->name }}.innerHTML.trim());$dispatch('showbox')">
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
            
                </x-input.group>
            @endif
        @elseif($type == 'create' || $type == 'edit')
            @if ($subfield->type != 'preset')        
                <x-input.group type="{{ $type }}" span="{{ $subfield->span }}" for="editing.{{ $subfield->name }}" label="{{ $subfield->label }}" help="{{ $subfield->help }}" description="{{ $subfield->description }}" required="{{ $subfield->required }}" hide="{{ $subfield->hide }}" moduleSection="{{ $moduleSection }}" moduleGroup="{{ $moduleGroup }}" module="{{ $module }}" :error="$errors->first('editing.' .$field->with. '.' .$key. '.' .$subfield->name)">
                    @switch($subfield->type)
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
                            <x-input.text key="{{ $key }}" subClass="{{ $field->with }}" subfieldName="{{ $subfield->name }}" source="editing" lazy="{{ $subfield->lazy }}" debounce="{{ $subfield->debounce }}" name="{{ $field->name }}" type="{{ $subfield->type != 'phoneNumber' ? $subfield->type : 'tel' }}" prepend="{{ $subfield->prepend }}" append="{{ $subfield->append }}" placeholder="{{ $subfield->placeholder }}" readonly="{{ $subfield->readonly }}" disabled="{{ $subfield->disabled }}" autofocus="{{ $subfield->autofocus }}" addable="{{ $subfield->addable }}" count="{{ $key + 1  }}" />
                            @break
                        @case('textarea')
                            <x-input.textarea key="{{ $key }}" subClass="{{ $field->with }}" subfieldName="{{ $subfield->name }}" source="editing" lazy="{{ $subfield->lazy }}" debounce="{{ $subfield->debounce }}" name="{{ $field->name }}" placeholder="{{ $subfield->placeholder }}" readonly="{{ $subfield->readonly }}" disabled="{{ $subfield->disabled }}" autofocus="{{ $subfield->autofocus }}" height="{{ $subfield->height }}" />
                            @break
                        @case('select')
                            <x-input.select key="{{ $key }}" subClass="{{ $field->with }}" subfieldName="{{ $subfield->name }}" source="editing" lazy="{{ $subfield->lazy }}" debounce="{{ $subfield->debounce }}" name="{{ $field->name }}" prepend="{{ $subfield->prepend }}" append="{{ $subfield->append }}" placeholder="{{ $subfield->placeholder }}" disabled="{{ $subfield->disabled }}" autofocus="{{ $subfield->autofocus }}">
                                @foreach($subfield->options as $option)
                                <option value="{{ $option->name }}">{{ $option->label }}</option>
                                @endforeach
                            </x-input.select>
                            @break
                        @case('radio')
                            <x-input.radio-group flex="{{ $subfield->flex }}" id="editing.{{ $field->with }}.{{ $key }}.{{ $subfield->name }}">
                                @foreach($subfield->options as $option)
                                <x-input.radio key="{{ $key }}" subClass="{{ $field->with }}" subfieldName="{{ $subfield->name }}" source="editing" lazy="{{ $subfield->lazy }}" debounce="{{ $subfield->debounce }}" name="{{ $field->name }}" option="{{ $subfield->name }}" disabled="{{ $subfield->disabled }}" value="{{ $option->name }}"  autofocus="{{ $subfield->autofocus }}">{{ $option->label }}</x-input.radio>
                                @endforeach
                            </x-input.radio-group>
                            @break
                        @case('checkbox')
                            <x-input.radio-group flex="{{ $subfield->flex }}" id="editing.{{ $field->with }}.{{ $key }}.{{ $subfield->name }}">
                                <x-input.checkbox key="{{ $key }}" subClass="{{ $field->with }}" subfieldName="{{ $subfield->name }}" source="editing" lazy="{{ $subfield->lazy }}" debounce="{{ $subfield->debounce }}" name="{{ $field->name }}" option="{{ $subfield->options[0]->name }}" disabled="{{ $subfield->disabled }}" value="{{ $subfield->options[0]->name }}"  autofocus="{{ $subfield->autofocus }}">{{ $subfield->options[0]->label }}</x-input.checkbox>
                            </x-input.radio-group>
                            @break
                        @case('checkboxMultiple')
                            <x-input.radio-group flex="{{ $subfield->flex }}" id="editing.{{ $field->with }}.{{ $key }}.{{ $subfield->name }}">
                                @foreach($subfield->options as $option)
                                <x-input.checkbox-multiple key="{{ $key }}" subClass="{{ $field->with }}" subfieldName="{{ $subfield->name }}" source="editing" lazy="{{ $subfield->lazy }}" debounce="{{ $subfield->debounce }}" name="{{ $field->name }}" option="{{ $option->name }}" disabled="{{ $subfield->disabled }}" value="{{ $subfield->name }}"  autofocus="{{ $subfield->autofocus }}">{{ $option->label }}</x-input.checkbox-multiple>
                                @endforeach
                            </x-input.radio-group>
                            @break
                    @endswitch
                </x-input.group>
            @endif
        @endif
    @endforeach
</div>

@if (!$loop->last)
<div>
    <div class="w-full border-t border-gray-300"></div>
</div>
@endif

@endforeach

@if($type == 'create' && isset($subClassItems[$field->with]))
    @if(isset($subClassFields) && sizeof($subClassFields) > 0)
    <div>
        <div class="w-full border-t border-gray-300"></div>
    </div>
    @endif

    @foreach ($subClassItems[$field->with] as $key => $item)
    <div class="text-gray-400 hover:text-gray-600 bg-gray-100 {{ $key == 0 && (sizeof($subClassFields) == 0) ? 'rounded-t-lg' : '' }} px-4 sm:px-6 pt-2 sm:pt-4 ">
        <div class="flex flex-row-reverse">
            <svg wire:click="removeSubClassField('{{ $field->with }}', {{$key}})" class="w-5 h-5 cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
            </svg> 
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-{{ $field->column }} gap-6 bg-gray-100 {{ isset($subClassItems[$field->with]) && sizeof($subClassItems[$field->with]) == $key + 1 ? 'rounded-b-lg' : '' }} px-4 sm:px-6 pb-10">

        @foreach($field->items as $subfield)
            @if ($subfield->type != 'preset') 
            <x-input.group subClass="{{ $field->with }}" type="{{ $type }}" span="{{ $subfield->span }}" for="subClassItems.{{ $subfield->name }}" label="{{ $subfield->label }}" help="{{ $subfield->help }}" description="{{ $subfield->description }}" required="{{ $subfield->required }}" hide="{{ $subfield->hide }}" moduleSection="{{ $moduleSection }}" moduleGroup="{{ $moduleGroup }}" module="{{ $module }}" :error="$errors->first('subClassItems.' .$field->with. '.' .$key. '.' .$subfield->name)">
                @switch($subfield->type)
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
                        <x-input.text key="{{ $key }}" subClass="{{ $field->with }}" subfieldName="{{ $subfield->name }}" source="subClassItems" lazy="{{ $subfield->lazy }}" debounce="{{ $subfield->debounce }}" name="{{ $field->name }}" type="{{ $subfield->type != 'phoneNumber' ? $subfield->type : 'tel' }}" prepend="{{ $subfield->prepend }}" append="{{ $subfield->append }}" placeholder="{{ $subfield->placeholder }}" readonly="{{ $subfield->readonly }}" disabled="{{ $subfield->disabled }}" autofocus="{{ $subfield->autofocus }}" addable="{{ $subfield->addable }}" count="{{ $key + 1  }}" />
                        @break
                    @case('textarea')
                        <x-input.textarea key="{{ $key }}" subClass="{{ $field->with }}" subfieldName="{{ $subfield->name }}" source="subClassItems" lazy="{{ $subfield->lazy }}" debounce="{{ $subfield->debounce }}" name="{{ $field->name }}" placeholder="{{ $subfield->placeholder }}" readonly="{{ $subfield->readonly }}" disabled="{{ $subfield->disabled }}" autofocus="{{ $subfield->autofocus }}" height="{{ $subfield->height }}" />
                        @break
                    @case('select')
                        <x-input.select key="{{ $key }}" subClass="{{ $field->with }}" subfieldName="{{ $subfield->name }}" source="subClassItems" lazy="{{ $subfield->lazy }}" debounce="{{ $subfield->debounce }}" name="{{ $subfield->name }}" prepend="{{ $subfield->prepend }}" append="{{ $subfield->append }}" placeholder="{{ $subfield->placeholder }}" disabled="{{ $subfield->disabled }}" autofocus="{{ $subfield->autofocus }}">
                            @foreach($subfield->options as $option)
                            <option value="{{ $option->name }}">{{ $option->label }}</option>
                            @endforeach
                        </x-input.select>
                        @break
                    @case('radio')
                        <x-input.radio-group flex="{{ $subfield->flex }}" id="subClassItems.{{ $field->with }}.{{ $key }}.{{ $subfield->name }}">
                            @foreach($subfield->options as $option)
                            <x-input.radio key="{{ $key }}" subClass="{{ $field->with }}" subfieldName="{{ $subfield->name }}" source="subClassItems" lazy="{{ $subfield->lazy }}" debounce="{{ $subfield->debounce }}" name="{{ $field->name }}" option="{{ $subfield->name }}" disabled="{{ $subfield->disabled }}" value="{{ $option->name }}"  autofocus="{{ $subfield->autofocus }}">{{ $option->label }}</x-input.radio>
                            @endforeach
                        </x-input.radio-group>
                        @break
                    @case('checkbox')
                        <x-input.radio-group flex="{{ $subfield->flex }}" id="subClassItems.{{ $field->with }}.{{ $key }}.{{ $subfield->name }}">
                            <x-input.checkbox key="{{ $key }}" subClass="{{ $field->with }}" subfieldName="{{ $subfield->name }}" source="subClassItems" lazy="{{ $subfield->lazy }}" debounce="{{ $subfield->debounce }}" name="{{ $field->name }}" option="{{ $subfield->options[0]->name }}" disabled="{{ $subfield->disabled }}" value="{{ $subfield->options[0]->name }}"  autofocus="{{ $subfield->autofocus }}">{{ $subfield->options[0]->label }}</x-input.checkbox>
                        </x-input.radio-group>
                        @break
                    @case('checkboxMultiple')
                        <x-input.radio-group flex="{{ $subfield->flex }}" id="subClassItems.{{ $field->with }}.{{ $key }}.{{ $subfield->name }}">
                            @foreach($subfield->options as $option)
                            <x-input.checkbox-multiple key="{{ $key }}" subClass="{{ $field->with }}" subfieldName="{{ $subfield->name }}" source="subClassItems" lazy="{{ $subfield->lazy }}" debounce="{{ $subfield->debounce }}" name="{{ $field->name }}" option="{{ $option->name }}" disabled="{{ $subfield->disabled }}" value="{{ $subfield->name }}"  autofocus="{{ $subfield->autofocus }}">{{ $option->label }}</x-input.checkbox-multiple>
                            @endforeach
                        </x-input.radio-group>
                        @break
                @endswitch
            </x-input.group>
            @endif
        @endforeach
        </div>

        @if (!$loop->last)
        <div>
            <div class="w-full border-t border-gray-300"></div>
        </div>
        @endif
    @endforeach
@endif

@else
    @if($type == 'show')
    <div class="flex items-center mt-1">
        <span class="text-sm text-gray-900"> - </span>
    </div>
    @endif
@endif

@php
    $tableHeaders = [];
    foreach($field->items as $subfield) {
        if ($subfield->type == 'preset') continue;
        $tableHeaders[] = $subfield->name;
    }
@endphp

@if($type == 'create')
<div class="mt-2 text-gray-400 hover:text-gray-600">
    <svg wire:click="addSubClassField('{{ $field->with }}', '{{ implode(',', $tableHeaders) }}')" class="w-5 h-5 cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
    </svg>
</div>
@endif

@endif
</div>