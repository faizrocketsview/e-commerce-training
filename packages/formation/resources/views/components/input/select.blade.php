@props([
    'source',
    'name',
    'key' => null,
    'subfieldName' => null,
    'subClass' => null,
    'prepend',
    'append',
    'disabled',
    'autofocus',
    'placeholder',
    'lazy' => false,
    'debounce' => null,
])

<div class="mt-1 rounded-md shadow-sm flex">
    @if ($prepend)
    <span class="bg-gray-50 border border-r-0 border-gray-300 rounded-l-md px-3 inline-flex items-center text-gray-500 text-sm">
        {{ $prepend }}
    </span>
    @endif

    <select wire:model{{ ($debounce?'.debounce.'.$debounce.'ms':($lazy?'.lazy':'.defer')) }}="{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}" id="{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}" name="{{ $source }}[{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}]{{ isset($key) ? '['.$key.']' : '' }}[{{ $subfieldName ? '['.$subfieldName.']' : '' }}" {{ $attributes->merge(['class' => 'block w-full border border-gray-300 py-2 px-3'.(!$prepend? ' rounded-l-md' : '').(!$append? ' rounded-r-md' : '').($disabled ? ' bg-gray-50' : '').' focus:border-blue-200 text-sm focus:outline-none focus:ring-blue-200']) }}{{ $disabled ? ' disabled' : ''}}{{ $autofocus ? ' autofocus' : ''}}>

    @if ($placeholder)
        <option value="" hidden>{{ $placeholder }}</option>
    @endif

    {{ $slot }}
    </select>

    @if ($append)
    <span class="bg-gray-50 border border-l-0 border-gray-300 rounded-r-md px-3 inline-flex items-center text-gray-500 text-sm">
        {{ $append }}
    </span>
    @endif
</div>