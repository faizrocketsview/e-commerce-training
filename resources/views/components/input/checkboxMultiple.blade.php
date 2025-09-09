@props([
    'source',
    'name',
    'key' => null,
    'subfieldName' => null,
    'subClass' => null,
    'options' => [],
    'disabled',
    'autofocus',
    'lazy' => false,
    'debounce' => null,
])

@if(isset($options) && count($options) > 0)
    @foreach($options as $option)
        <div class="flex mr-8 mb-2">
            <input wire:model{{ ($debounce?'.debounce.'.$debounce.'ms':($lazy?'.lazy':'.defer')) }}="{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}.{{ $option->value }}" id="{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}_{{ $option->value }}" name="{{ $source }}[{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}]{{ isset($key) ? '['.$key.']' : '' }}[{{ $subfieldName ? '['.$subfieldName.']' : '' }}]" {{ $attributes->merge(['class' => 'mt-0.5 focus:ring-blue-200 h-4 w-4 text-indigo-600 border-gray-300']) }}{{ $disabled ? ' disabled' : ''}}{{ $autofocus ? ' autofocus' : ''}} type="checkbox" value="{{ $option->value }}">
            <label for="{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}_{{ $option->value }}" class="ml-3 block text-sm font-medium text-gray-700">
                {{ $option->label }}
            </label>
        </div>
    @endforeach
@else
    <div class="text-gray-500 text-sm">No options available</div>
@endif