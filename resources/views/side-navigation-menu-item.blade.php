                    <div class="flex-shrink-0 flex items-center px-4 space-x-4">
                        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="block h-8 w-auto">
                            <path d="M11.395 44.428C4.557 40.198 0 32.632 0 24 0 10.745 10.745 0 24 0a23.891 23.891 0 0113.997 4.502c-.2 17.907-11.097 33.245-26.602 39.926z" fill="#6875F5"></path>
                            <path d="M14.134 45.885A23.914 23.914 0 0024 48c13.255 0 24-10.745 24-24 0-3.516-.756-6.856-2.115-9.866-4.659 15.143-16.608 27.092-31.75 31.751z" fill="#6875F5"></path>
                        </svg>
                        <div class="text-xl text-white">{{ config('app.name') }}</div>
                    </div>
    
                    <nav class="flex-1 flex flex-col divide-y divide-gray-700 overflow-y-auto pt-2" aria-label="Sidebar">
                        <x-side-navigation-menu.module-section>
                            <x-side-navigation-menu.module-group name="dashboard" href="/dashboard">
                                <x-slot name="icon">
                                    <svg class="mr-4 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                    </svg>
                                </x-slot>
                            </x-side-navigation-menu.module-group>
                        </x-side-navigation-menu.module-section>

                        @canany(['ecommerce.managements.categories:show','ecommerce.managements.products:show','ecommerce.managements.orders:show','ecommerce.managements.items:show','ecommerce.managements.users:show'])
                        <x-side-navigation-menu.module-section name="ecommerce">

                            @canany(['ecommerce.managements.categories:show','ecommerce.managements.products:show','ecommerce.managements.orders:show','ecommerce.managements.items:show','ecommerce.managements.users:show'])
                            <x-side-navigation-menu.module-group sectionname="ecommerce" name="managements">
                                <x-slot name="icon">
                                    <svg class="mr-4 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75" />
                                    </svg>
                                </x-slot>

                                                                
                                @can('ecommerce.managements.users:show')
                                <x-side-navigation-menu.module sectionname="ecommerce" groupname="managements" name="users" href="/ecommerce/managements/users" />
                                @endcan

                                @can('ecommerce.managements.categories:show')
                                <x-side-navigation-menu.module sectionname="ecommerce" groupname="managements" name="categories" href="/ecommerce/managements/categories" />
                                @endcan
                                
                                @can('ecommerce.managements.products:show')
                                <x-side-navigation-menu.module sectionname="ecommerce" groupname="managements" name="products" href="/ecommerce/managements/products" />
                                @endcan
                                
                                @can('ecommerce.managements.orders:show')
                                <x-side-navigation-menu.module sectionname="ecommerce" groupname="managements" name="orders" href="/ecommerce/managements/orders" />
                                @endcan
                               
                            </x-side-navigation-menu.module-group>
                            @endcanany

                        </x-side-navigation-menu.module-section>
                        @endcanany

                    </nav>