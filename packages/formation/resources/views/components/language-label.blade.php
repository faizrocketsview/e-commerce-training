@props([
    'lang',
    'size' => 'xs',
    'highlight' => false,
    'border' => true,
    'bgColor' => 'bg-white', //'bg-gray-100'
])

<span class="block text-center align-middle @if($size == 'sm') w-12 max-w-12 h-6 max-h-6 @elseif($size == 'xs') w-10 max-w-10 h-5 max-h-5 @endif {{ $bgColor }} @if($border) border border-gray-300 @endif @if($highlight) group-hover:bg-gray-50 group-hover:border-gray-300 @endif rounded-full px-2.5 mr-1 text-{{$size}} text-gray-700">
    {{ $lang }}
</span>