<table class="min-w-full divide-y divide-gray-200 sm:border-t sm:border-gray-200">
    <thead>
        <tr>
            {{ $head }}
        </tr>
    </thead>
    <tbody wire:sortable="setOrderedList" class="divide-y divide-gray-200">
        {{ $body }}
    </tbody>
</table>