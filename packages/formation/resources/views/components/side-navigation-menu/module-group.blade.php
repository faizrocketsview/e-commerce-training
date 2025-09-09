@props([
    'sectionname' => '',
    'name',
    'href' => '#',
])
<div x-data="{ isSideNavigationMenuItemOpen: {{(request()->moduleSection == $sectionname && request()->moduleGroup == $name) ? 'true' : 'false'}} }">
    <a href="{{ $href }}" class="group flex items-center px-2 py-2 hover:bg-gray-700 text-white text-sm font-medium rounded-md"
        @click="isSideNavigationMenuItemOpen = !isSideNavigationMenuItemOpen">
        {{ $icon }}
        {{ __('menu.'.$name) }}
    </a>
    <div class="space-y-1" x-show="isSideNavigationMenuItemOpen" {!! (request()->moduleSection == $sectionname && request()->moduleGroup == $name) ? '' : 'style="display: none;"' !!}>
        
        {{ $slot }}
    </div>
</div>