@props([
    'sortable' => null,
    'direction' => null,
    'align' => null,
])

<th {{ $attributes->merge(['class' => 'py-3 bg-gray-50'])->only('class') }}><!-rounded-tr-lg sm:rounded-none>
    @unless($sortable)
    <span class="flex flex-wrap text-xs leading-4 font-medium text-left text-gray-500 uppercase tracking-wider {{ $align == 'right' ? 'float-right' : '' }}">
        {{ $slot }}
    </span>

    @else
    <button {{ $attributes->except('class') }} class="flex flex-nowrap text-xs leading-4 font-medium text-left text-gray-500 uppercase tracking-wider group focus:outline-none {{ $align == 'right' ? 'float-right' : '' }}">
        {{ $slot }}

        @if ($direction === "asc")
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2 14l4-4 4 4m0 4" />
        </svg>

        @elseif ($direction === "desc")
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2 4m8 6l-4 4-4-4" />
        </svg>

        @else
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2 9l4-4 4 4m0 6l-4 4-4-4" />
        </svg>

        @endif
    </button>
    @endif
</th>