@props([
    'sectionname',
    'groupname',
    'name',
    'href' => '',
])
<a href="{{ ($href=='' ? '/'.$sectionname.'/'.$groupname.'/'.$name : $href) }}" class="group w-full flex items-center pl-14 pr-2 py-2 {{request()->moduleSection == $sectionname && request()->moduleGroup == $groupname && request()->module == $name ? 'bg-gray-900' : 'hover:bg-gray-700'}} font-medium text-sm text-white rounded-md">
    {{ __('menu.'.$name) }}
</a>