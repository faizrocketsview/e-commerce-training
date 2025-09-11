<div class="min-h-screen bg-gray-50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">Explore Our Catalog</h1>
            <p class="text-gray-500 mt-1">Browse products with beautiful imagery and quick details.</p>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex-1">
                @if(isset($this->index->search))
                    <div class="relative">
                        <input type="text" wire:model.debounce.500ms="search" placeholder="Search products, e.g. iPhone, MacBook…"
                               class="w-full rounded-xl border-gray-200 bg-white shadow-sm px-4 py-3 text-sm sm:text-base focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 placeholder:text-gray-400">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-xs text-gray-400">Enter ↵</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($this->items as $product)
                @php
                    $img = $product->image ?? null;
                    $imgUrl = null;
                    if ($img) {
                        // Formation stores files as just filename in the image column
                        // The folderPath is 'products/' as defined in ProductFormation.php
                        $key = 'products/' . $img;
                        try {
                            if (\Storage::disk('s3')->exists($key)) {
                                $imgUrl = \Storage::disk('s3')->temporaryUrl($key, now()->addDay());
                            }
                        } catch (\Exception $e) {
                            \Log::error('S3 image error for product ' . $product->id . ': ' . $e->getMessage());
                        }
                    }
                    // Debug: Log image data
                    \Log::info('Product: ' . $product->name . ' (ID: ' . $product->id . '), Image: ' . ($img ?? 'null') . ', Key: ' . ($img ? 'products/' . $img : 'null') . ', Exists: ' . ($img ? (\Storage::disk('s3')->exists('products/' . $img) ? 'yes' : 'no') : 'n/a') . ', URL: ' . ($imgUrl ?? 'null'));
                @endphp
                <div class="group bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 hover:shadow-lg transition-all duration-300 p-4 flex flex-col">
                    <!-- Product Image Box - Always Visible -->
                    <div class="relative rounded-xl overflow-hidden bg-gray-100 mb-4 h-48 border-2 border-gray-200">
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
                                <div class="text-center p-4">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm text-gray-500 font-medium">No image available</p>
                                    <p class="text-xs text-gray-400 mt-1">Product ID: {{ $product->id }}</p>
                                </div>
                            </div>
                        @endif
                        @php
                            $rawStatus = strtolower($product->status ?? '');
                            $inStock = in_array($rawStatus, ['active','available','in_stock']);
                        @endphp
                        <div class="absolute top-3 left-3 text-[10px] sm:text-xs px-2 py-0.5 rounded-full bg-white/90 text-gray-700 shadow">{{ strtoupper($product->status) }}</div>
                    </div>

                    <div class="mt-4 flex-1 flex flex-col">
                        <h3 class="text-base sm:text-lg font-semibold text-slate-900">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 line-clamp-2 mt-1">{{ $product->description }}</p>
                        <div class="mt-4 flex items-center justify-between">
                            <div class="text-indigo-600 font-bold">${{ number_format((float) $product->price, 2) }}</div>
                            <button class="inline-flex items-center gap-2 text-sm text-white bg-indigo-600 px-3 py-1.5 rounded-lg shadow hover:bg-indigo-500 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-300" wire:click="show({{ $product->id }})">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View details
                            </button>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-xs text-gray-500">
                                @if($inStock)
                                    @php
                                        $stock = $product->stock ?? 0;
                                        $stockText = $stock > 0 ? $stock . ' left' : 'In stock';
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $stockText }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 ring-1 ring-rose-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                        Out of stock
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $this->items->links() }}
        </div>
    </div>
</div>


