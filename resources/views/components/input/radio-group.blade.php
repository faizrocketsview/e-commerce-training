@props([
    'flex',
])

<div class="mt-3{{ $flex ? ' flex flex-wrap' : '' }}" {{ $attributes }}>
    {{ $slot }}
</div>