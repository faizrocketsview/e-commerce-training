@props([
    'source',
    'name',
    'key' => null,
    'subfieldName' => null,
    'subClass' => null,
    'id',
    'name',
    'height',
    'readonly',
    'disabled',
    'autofocus',
    'value',
    'type',
    'lazy' => false,
    'debounce' => null,
])

<div wire:ignore {{ $attributes }} x-data class="mt-1 rounded-md shadow-sm">
    @if ($type == "show" || $disabled || $readonly)
        <div {{ $attributes->merge(['class' => 'trix-editor block w-full border border-gray-300 py-2 px-3 rounded-md focus:border-blue-200 focus:ring-1 text-sm focus:outline-none focus:ring-blue-200 resize-none'.($readonly || $disabled ? ' bg-gray-50' : '')]) }}>
            {!!html_entity_decode($value)!!}
        </div>
    @else
        <input wire:model{{ ($debounce?'.debounce.'.$debounce.'ms':($lazy?'.lazy':'.defer')) }}="{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}" id="{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}" {{ $readonly ? ' readonly' : ''}}{{ $disabled ? ' disabled' : ''}} type="hidden" />

        <trix-editor input="{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}" {{ $attributes->merge(['class' => 'block w-full border border-gray-300 py-2 px-3 rounded-md focus:border-blue-200 focus:ring-1 text-sm focus:outline-none focus:ring-blue-200 resize-none'.($readonly || $disabled ? ' bg-gray-50' : '')]) }}{{ $height ? ' style=height:'.$height.'px' : ''}}{{ $readonly ? ' readonly' : ''}}{{ $disabled ? ' disabled' : ''}}{{ $autofocus ? ' autofocus' : ''}}></trix-editor> 
    @endif

    <script type="module">
        addEventListener("trix-initialize", function(e) {
            //remove file upload capability 
            const file_tools = document.querySelector(".trix-button-group--file-tools");
            if (file_tools) { file_tools.remove(); }

            var trixEditor = document.getElementById("{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}");
            if (e.target.getAttribute('input') === "{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}") {
                var stored = trixEditor.getAttribute('value');
                e.target.editor.loadHTML(stored);
            }
        })

        //remove file upload capability 
        addEventListener("trix-file-accept", function(e) {
            e.preventDefault();
        })
        
        addEventListener("trix-blur", function(e) {
            var trixEditor = document.getElementById("{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}");
            @this.set('{{ $source }}.{{ (isset($subClass) && $subClass != '') ? $subClass : $name }}{{ isset($key) ? '.'.$key : '' }}{{ $subfieldName ? '.'.$subfieldName : '' }}', trixEditor.getAttribute('value'));
        })
    </script>
</div>