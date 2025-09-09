<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => 'py-2 px-4 border rounded-md text-sm leading-5 font-medium capitalize focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-200' . ($attributes->get('disabled') ? ' opacity-75 cursor-not-allowed' : ''),
    ]) }}>
    {{ $slot }}
</button>