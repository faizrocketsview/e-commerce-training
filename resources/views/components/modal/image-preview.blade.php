@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">

        <div class="mt-3 text-center sm:mt-0 sm:text-left">
            <h3 class="text-lg">
                Preview Image
            </h3>

            <div class="mt-2 overflow-y-auto" style="max-height: calc(100vh - 200px); -webkit-overflow-scrolling: touch;">
                {{ $content }}
            </div>
        </div>

    </div>
    <div class="px-6 py-4 bg-gray-100 text-right space-x-2">
        {{ $footer }}
    </div>
</x-modal>