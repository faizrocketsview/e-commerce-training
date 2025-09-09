@props([
    'source',
    'name',
    'key' => null,
    'subfieldName' => null,
    'subClass' => null,
    'height',
    'readonly',
    'disabled',
    'autofocus',
    'lazy' => false,
    'debounce' => null,
    'lang' => null,
])

<div class="mt-1 rounded-md shadow-sm flex">
    <textarea wire:model{{ ($debounce?'.debounce.'.$debounce.'ms':($lazy?'.lazy':'.defer')) }}="{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ isset($lang) ? '.'.$lang : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}" id="{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ isset($lang) ? '.'.$lang : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}" name="{{ $source }}[{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}]{{ isset($key) ? '['.$key.']' : '' }}[{{ $subfieldName ? '['.$subfieldName.']' : '' }}" {{ $attributes->merge(['class' => 'block w-full border border-gray-300 py-2 px-3 rounded-md focus:border-blue-200 text-sm focus:outline-none focus:ring-blue-200 resize-none'.($readonly || $disabled ? ' bg-gray-50' : '')]) }}{{ $height ? ' style=height:'.$height.'px' : ''}}{{ $readonly ? ' readonly' : ''}}{{ $disabled ? ' disabled' : ''}}{{ $autofocus ? ' autofocus' : ''}}>
    </textarea>
</div>