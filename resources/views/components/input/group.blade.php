@props([
    'type',
    'span' => 1,
    'label',
    'for',
    'help',
    'description',
    'required',
    'hide',
    'module',
    'moduleGroup',
    'moduleSection',
    'error' => false,
    'lang' => null,
])


<div class="col-span-1 sm:col-span-{{ $span }}{{ $hide ? ' hidden' : '' }}">
    <label for="{{ $for }}" class="flex item-center">
        <span class="mr-1 text-sm font-medium text-gray-700">{{ Lang::has($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$label) ? ucfirst(__($moduleSection.'/'.$moduleGroup.'/'.$module.'.'.$label)) : ucfirst(__('main.'.$label)) }}</span>

        @if($lang)
            <x-language-label :lang="$lang"></x-language-label>
        @endif
        
        @if($required && ($type == 'create' || $type == 'edit'))
        <span class="mr-1 text-sm font-medium text-red-500">*</span>
        @endif
        
        @if($help)
        <div x-data="{ open: false }" @keydown.escape.stop="open = false"
            @click.away="open = false"
            class="relative flex justify-end items-center">
            <button type="button"
                class="w-4 h-4 bg-white inline-flex items-center justify-center text-gray-400 rounded-full hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-200"
                @click="open = !open"
                aria-haspopup="true" x-bind:aria-expanded="open">
                <span class="sr-only">Open options</span>
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                </svg>
            </button>
            <div x-description="Dropdown menu, show/hide based on menu state."
                x-show="open" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="mx-3 origin-top-right absolute left-7 top-0 w-60 mt-1 rounded-md shadow-lg z-20 bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-200 focus:outline-none"
                role="menu" aria-orientation="vertical"
                aria-labelledby="project-options-menu-0" style="display: none;">
                <div class="px-4 py-2 text-sm text-gray-700" role="none">
                    {!! htmlspecialchars_decode($help) !!}
                </div>
            </div>
        </div>
        @endif

    </label>
    
    {{ $slot }}

    @if ($error)
    <div class="mt-1 text-red-500 text-sm">{{ $error }}</div>
    @endif

    @if($description)
    <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
    @endif
</div>