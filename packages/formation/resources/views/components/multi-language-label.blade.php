@props(['moduleSection', 'moduleGroup', 'module', 'translations' => [], 'localize' => false, 'allowedLangs' => null])

@php
    $langs = $allowedLangs ?: ['en'];
@endphp

<div class="flex flex-col">
    @foreach($langs as $lang)
        @php $val = $translations[$lang] ?? null; @endphp
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">{{ $lang }}</span>
            <span>{{ $localize ? ($val ? __($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$val) : '-') : ($val ?? '-') }}</span>
        </div>
    @endforeach
</div>

@props([
    'moduleSection',
    'moduleGroup',
    'module',
    'translations' => [],
    'localize' => false,
    'highlight' => false,
    'allowedLangs',
])

@php
    if(isset($allowedLangs)) {
        if(gettype($allowedLangs) === 'string') {
            $allowedLangs = [$allowedLangs];
        } elseif(gettype($allowedLangs) === 'boolean') {
            $allowedLangs = array_keys($translations);
        }
    } else {
        $allowedLangs = array_keys($translations);
    }
@endphp

@foreach($translations as $lang => $value)
    @if(in_array($lang, $allowedLangs))
        <div class="flex flex-row first:mt-0 mt-1.5 group items-center">
            <x-language-label :lang="$lang" :highlight="$highlight"></x-language-label>
            <p class="@if($highlight) group-hover:text-gray-900 @endif align-middle">
                @if($localize) 
                    {{ __($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$value) }} 
                @else 
                    {{ $value }}
                @endif
            </p>
        </div>
    @endif
@endforeach