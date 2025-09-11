<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('E-Commerce Dashboard') }}
        </h2>
            <div class="text-sm text-gray-500">
                Last updated: {{ now()->format('M d, Y H:i') }}
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
                <!-- Total Revenue -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                                    <dd class="text-lg font-semibold text-gray-900">$24,567.89</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-sm text-green-600 font-medium">+12.5%</span>
                            <span class="text-sm text-gray-500">from last month</span>
                        </div>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                                    <dd class="text-lg font-semibold text-gray-900">1,234</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-sm text-green-600 font-medium">+8.2%</span>
                            <span class="text-sm text-gray-500">from last month</span>
                        </div>
                    </div>
                </div>

                <!-- Active Products -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Products</dt>
                                    <dd class="text-lg font-semibold text-gray-900">89</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-sm text-green-600 font-medium">+3</span>
                            <span class="text-sm text-gray-500">new this week</span>
                        </div>
                    </div>
                </div>

                <!-- Conversion Rate -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Conversion Rate</dt>
                                    <dd class="text-lg font-semibold text-gray-900">3.24%</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-sm text-red-600 font-medium">-0.5%</span>
                            <span class="text-sm text-gray-500">from last month</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Analytics -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
                <!-- Revenue Chart -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Revenue Overview</h3>
                            <div class="flex space-x-2">
                                <button onclick="updateRevenueChart('7d')" class="px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-100 rounded-full">7D</button>
                                <button onclick="updateRevenueChart('30d')" class="px-3 py-1 text-xs font-medium text-gray-500 hover:text-gray-700">30D</button>
                                <button onclick="updateRevenueChart('90d')" class="px-3 py-1 text-xs font-medium text-gray-500 hover:text-gray-700">90D</button>
                            </div>
                        </div>
                        <div class="relative h-64">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Orders Trend Chart -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Orders Trend</h3>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span class="text-sm text-gray-500">Last 7 days</span>
                            </div>
                        </div>
                        <div class="relative h-64">
                            <canvas id="ordersChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Product Performance Chart -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Top Products</h3>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-gray-500">Sales Volume</span>
                            </div>
                        </div>
                        <div class="relative h-64">
                            <canvas id="productsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders and Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
                <!-- Recent Orders -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                            <a href="/ecommerce/managements/orders" class="text-sm text-indigo-600 hover:text-indigo-700">View all</a>
                        </div>
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">#1234</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">John Doe</td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">$249.99</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">#1233</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Jane Smith</td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">$89.50</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">#1232</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Mike Johnson</td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Shipped</span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">$156.75</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="/ecommerce/managements/products?type=create" class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                Add New Product
                            </a>
                            <a href="/ecommerce/managements/orders?type=create" class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                                Create Order
                            </a>
                            <a href="/ecommerce/managements/products" class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                Manage Products
                            </a>
                            <a href="/ecommerce/managements/orders" class="flex items-center p-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                View Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Alerts -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Inventory Alerts</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">3 Low Stock</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-center p-3 bg-red-50 rounded-lg border border-red-200">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-red-900">AirPods Pro</p>
                                <p class="text-xs text-red-600">Only 2 left in stock</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-yellow-900">MacBook Air</p>
                                <p class="text-xs text-yellow-600">5 units remaining</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-orange-50 rounded-lg border border-orange-200">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-orange-900">iPhone 15</p>
                                <p class="text-xs text-orange-600">8 units remaining</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Mock Data
        const revenueData = {
            '7d': {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                data: [1200, 1900, 800, 2100, 1600, 2500, 1800]
            },
            '30d': {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                data: [8500, 9200, 7800, 10500]
            },
            '90d': {
                labels: ['Month 1', 'Month 2', 'Month 3'],
                data: [24500, 31200, 28900]
            }
        };

        const ordersData = {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            data: [45, 52, 38, 67, 58, 72, 49]
        };

        const productsData = {
            labels: ['AirPods Pro', 'MacBook Air', 'iPhone 15', 'iPad Pro', 'Apple Watch'],
            data: [45, 23, 38, 15, 28]
        };

        // Chart configurations
        let revenueChart, ordersChart, productsChart;

        // Revenue Chart
        function createRevenueChart(period = '7d') {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            const data = revenueData[period];
            
            if (revenueChart) {
                revenueChart.destroy();
            }

            revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Revenue ($)',
                        data: data.data,
                        borderColor: 'rgb(99, 102, 241)',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(99, 102, 241)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }

        // Orders Chart
        function createOrdersChart() {
            const ctx = document.getElementById('ordersChart').getContext('2d');
            
            ordersChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ordersData.labels,
                    datasets: [{
                        label: 'Orders',
                        data: ordersData.data,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(59, 130, 246, 0.8)'
                        ],
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 0,
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Products Chart
        function createProductsChart() {
            const ctx = document.getElementById('productsChart').getContext('2d');
            
            productsChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: productsData.labels,
                    datasets: [{
                        data: productsData.data,
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(168, 85, 247, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderColor: [
                            'rgb(34, 197, 94)',
                            'rgb(59, 130, 246)',
                            'rgb(168, 85, 247)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)'
                        ],
                        borderWidth: 2,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }

        // Update Revenue Chart
        function updateRevenueChart(period) {
            // Update button states
            document.querySelectorAll('[onclick^="updateRevenueChart"]').forEach(btn => {
                btn.className = 'px-3 py-1 text-xs font-medium text-gray-500 hover:text-gray-700';
            });
            event.target.className = 'px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-100 rounded-full';
            
            createRevenueChart(period);
        }

        // Initialize all charts when page loads
        document.addEventListener('DOMContentLoaded', function() {
            createRevenueChart('7d');
            createOrdersChart();
            createProductsChart();
        });
    </script>
</x-app-layout>
