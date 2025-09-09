<div class="relative flex-shrink-0 flex h-16 bg-white border-b border-gray-200 lg:border-none">
    <button @click="isSideNavigationMenuOpen = !isSideNavigationMenuOpen"
        class="px-4 border-r border-gray-200 text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-200 lg:hidden">
        <span class="sr-only">Open sidebar</span>
        <!-- Heroicon name: outline/menu-alt-1 -->
        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h8m-8 6h16" />
        </svg>
    </button>
    <!-- Search bar -->
    <div class="flex-1 px-4 flex justify-between max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex-1 flex">
            <!--
            <form class="w-full flex md:ml-0" action="#" method="GET">
                <label for="search_field" class="sr-only">Search</label>
                <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                    <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none"
                        aria-hidden="true">-->
                        <!-- Heroicon name: solid/search -->
                        <!--<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input id="search_field" name="search_field"
                        class="block w-full h-full pl-8 pr-3 py-2 border-transparent text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-0 focus:border-transparent text-sm"
                        placeholder="Search transactions" type="search">
                </div>
            </form>
            -->
        </div>
        <div class="ml-4 flex items-center md:ml-6">
            <button class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-200">
                <span class="sr-only">View notifications</span>
                <!-- Heroicon name: outline/bell -->
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>
            
            <!-- Teams Dropdown -->
            @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
            <div x-data="{ isProfileOpen: false }" @keydown.escape.stop="isProfileOpen = false"
                @click.away="isProfileOpen = false" class="ml-3 relative">
                <div>
                    <button @click="isProfileOpen = !isProfileOpen" type="button"
                        class="max-w-xs text-gray-400 hover:text-gray-500 bg-white rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-200 lg:p-2 lg:rounded-md lg:hover:bg-gray-50"
                        id="user-menu" aria-expanded="false" aria-haspopup="true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 md:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="hidden ml-3 text-gray-700 text-sm font-medium md:block">{{ Auth::user()->currentTeam->name }}</span>
                        <!-- Heroicon name: solid/chevron-down -->
                        <svg class="hidden flex-shrink-0 ml-1 h-5 w-5 text-gray-400 md:block"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div x-show="isProfileOpen" 
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="z-10 origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                    role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>
                    <a href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                        {{ __('Team Settings') }}
                    </a>

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                    <a href="{{ route('teams.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                        {{ __('Create New Team') }}
                    </a>
                    @endcan

                    <div class="border-t border-gray-100"></div>
                    
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Switch Teams') }}
                    </div>

                    @foreach (Auth::user()->allTeams() as $team)
                        <x-switchable-team :team="$team" />
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Profile dropdown -->
            <div x-data="{ isProfileOpen: false }" @keydown.escape.stop="isProfileOpen = false"
                @click.away="isProfileOpen = false" class="ml-3 relative">
                <div>
                    <button @click="isProfileOpen = !isProfileOpen" type="button"
                        class="max-w-xs bg-white rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-200 lg:p-2 lg:rounded-md lg:hover:bg-gray-50"
                        id="user-menu" aria-expanded="false" aria-haspopup="true">
                        <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                        <span class="hidden ml-3 text-gray-700 text-sm font-medium md:block"><span
                                class="sr-only">Open user
                                menu for </span>{{ Auth::user()->name }}</span>
                        <!-- Heroicon name: solid/chevron-down -->
                        <svg class="hidden flex-shrink-0 ml-1 h-5 w-5 text-gray-400 md:block"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div x-show="isProfileOpen" 
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="z-10 origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                    role="menu" aria-orientation="vertical" aria-labelledby="user-menu" style="display: none;">
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Account') }}
                    </div>
                    <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                        {{ __('Profile') }}
                    </a>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <a href="{{ route('api-tokens.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                        {{ __('API Tokens') }}
                    </a>
                    @endif

                    <div class="border-t border-gray-100"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" href="{{ route('logout') }}" 
                            onclick="event.preventDefault();
                            this.closest('form').submit();">
                                {{ __('Logout') }}
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>