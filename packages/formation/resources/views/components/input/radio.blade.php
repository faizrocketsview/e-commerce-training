@props([
    'source',
    'name',
    'key' => null,
    'subfieldName' => null,
    'subClass' => null,
    'option',
    'disabled',
    'autofocus',
    'lazy' => false,
    'debounce' => null,
])

<div class="flex mr-8 mb-2 mt-0.5">
    <input wire:model{{ ($debounce?'.debounce.'.$debounce.'ms':($lazy?'.lazy':'.defer')) }}="{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}" id="{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}_{{$option}}" name="{{ $source }}[{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}]{{ isset($key) ? '['.$key.']' : '' }}{{ $subfieldName ? '['.$subfieldName.']' : '' }}" {{ $attributes->merge(['class' => 'mt-0.5 focus:ring-blue-200 h-4 w-4 text-indigo-600 border-gray-300']) }}{{ $disabled ? ' disabled' : ''}}{{ $autofocus ? ' autofocus' : ''}} type="radio">    
    <label for="{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}_{{$option}}" class="ml-3 block text-sm font-medium text-gray-700">
        {{ $slot }}
    </label>
</div>