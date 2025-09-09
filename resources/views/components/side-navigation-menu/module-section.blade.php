@props([
    'name' => '',
])
<div class="pt-4 mb-6">
    <div class="px-2 space-y-1">
        @if (!empty($name))
        <div class="flex items-center px-2 py-2 text-gray-500 text-sm font-semibold uppercase tracking-widest">{{ __('menu.'.$name) }}</div>
        @endif
        {{ $slot }}
    </div>
</div>