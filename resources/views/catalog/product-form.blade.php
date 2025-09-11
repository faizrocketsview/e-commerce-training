<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
        <!-- Back Button -->
        <a href="/catalog/ecommerce/managements/products" class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-700 mb-4 sm:mb-6">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Catalog
        </a>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            <!-- Image Section -->
            <div class="order-2 lg:order-1">
                @php
                    $img = $editing->image ?? null;
                    $imgUrl = null;
                    if ($img) {
                        $key = 'products/' . $img;
                        try {
                            if (\Storage::disk('s3')->exists($key)) {
                                $imgUrl = \Storage::disk('s3')->temporaryUrl($key, now()->addDay());
                            }
                        } catch (\Exception $e) {
                            \Log::error('S3 image error for product ' . $editing->id . ': ' . $e->getMessage());
                        }
                    }
                @endphp
                
                <div class="relative rounded-xl overflow-hidden bg-gray-100 shadow-lg border-2 border-gray-200 h-64 sm:h-80 lg:h-96">
                    @if($imgUrl)
                        <img src="{{ $imgUrl }}" alt="{{ $editing->name }}" class="w-full h-full object-cover" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
                            <div class="text-center p-4">
                                <svg class="mx-auto h-16 w-16 text-gray-400 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm text-gray-500 font-medium">No image available</p>
                            </div>
                        </div>
                    @endif
                    <div class="absolute top-3 left-3 px-2 py-1 rounded-full text-xs font-medium bg-white/90 text-gray-700 shadow">
                        {{ strtoupper($editing->status) }}
                    </div>
                </div>
            </div>

            <!-- Product Details Section -->
            <div class="order-1 lg:order-2">
                <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4 sm:p-6">
                    <!-- Product Header -->
                    <div class="mb-4 sm:mb-6">
                        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-2">{{ $editing->name }}</h1>
                        <div class="text-xl sm:text-2xl text-indigo-600 font-bold mb-3">${{ number_format((float) $editing->price, 2) }}</div>
                        <div class="inline-flex items-center gap-2 px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs sm:text-sm">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            In Stock
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-6 text-xs sm:text-sm">
                        <div>
                            <span class="text-gray-500">SKU:</span>
                            <span class="text-gray-900 font-mono">{{ $editing->sku }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Stock:</span>
                            <span class="text-gray-900">{{ $editing->stock ?? 0 }} units</span>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($editing->description)
                        <div class="mb-4 sm:mb-6">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Description</h3>
                            <p class="text-sm sm:text-base text-gray-600">{{ $editing->description }}</p>
                        </div>
                    @endif

                    <!-- Buy Now Button -->
                    <div class="text-center">
                        <a href="/ecommerce/managements/orders?type=create" 
                           class="inline-flex items-center justify-center gap-2 text-white bg-indigo-600 px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg shadow-lg border-2 border-indigo-700 hover:bg-indigo-500 hover:border-indigo-600 focus:ring-2 focus:ring-indigo-300 transition-all duration-200 w-full sm:w-auto">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M9 7l-6 14h18L15 7m-6 0V3m6 4V3"/>
                            </svg>
                            Buy Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


