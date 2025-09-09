        <div x-show="isSideNavigationMenuOpen" 
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" 
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" 
            class="fixed inset-0 flex z-40 lg:hidden" role="dialog" aria-modal="true">

            <div x-show="isSideNavigationMenuOpen" 
                x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="-translate-x-full" 
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-x-0" 
                x-transition:leave-end="-translate-x-full"
                @click="isSideNavigationMenuOpen = !isSideNavigationMenuOpen" 
                class="fixed inset-0 bg-gray-600 bg-opacity-75" aria-hidden="true"></div>

            <div x-show="isSideNavigationMenuOpen" 
                x-transition:enter="ease-in-out duration-300" 
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in-out duration-300"
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0"
                class="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-gray-800">

                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button @click="isSideNavigationMenuOpen = !isSideNavigationMenuOpen"
                        class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Close sidebar</span>
                        <!-- Heroicon name: outline/x -->
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @include('side-navigation-menu-item')

            </div>

            <div class="flex-shrink-0 w-14" aria-hidden="true">
                <!-- Dummy element to force sidebar to shrink to fit close icon -->
            </div>
        </div>

        <!-- Static sidebar for desktop -->
        <div class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-64">
                <!-- Sidebar component, swap this element with another sidebar if you like -->
                <div class="flex flex-col flex-grow bg-gray-800 pt-5 pb-4 overflow-y-auto">

                    @include('side-navigation-menu-item')
                    
                </div>
            </div>
        </div>