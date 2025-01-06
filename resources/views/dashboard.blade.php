<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Posts Stats -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-500 bg-opacity-10">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="font-semibold text-gray-600">Total Posts</h2>
                            <p class="text-2xl font-bold text-gray-800">{{ $totalPosts }}</p>
                            <p class="text-sm text-green-500">+{{ $postsThisWeek }} this week</p>
                        </div>
                    </div>
                </div>

                <!-- User Engagement -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-500 bg-opacity-10">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="font-semibold text-gray-600">Total Views</h2>
                            <p class="text-2xl font-bold text-gray-800">{{ $totalViews }}</p>
                            <p class="text-sm text-green-500">Avg {{ $avgViewsPerPost }} per post</p>
                        </div>
                    </div>
                </div>

                <!-- System Health -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-500 bg-opacity-10">
                            <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="font-semibold text-gray-600">System Health</h2>
                            <p class="text-2xl font-bold text-gray-800">{{ round((100 - $systemMetrics['cpu']), 1) }}%
                            </p>
                            <p class="text-sm text-purple-500">CPU Load: {{ $systemMetrics['cpu'] }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Response Time -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-500 bg-opacity-10">
                            <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="font-semibold text-gray-600">Avg Response Time</h2>
                            <p class="text-2xl font-bold text-gray-800">127ms</p>
                            <p class="text-sm text-yellow-500">-23ms from last week</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add this after your stats cards grid and before the activity feed -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Posts Timeline</h3>
                    <div class="flex space-x-2">
                        <button class="chart-type-btn px-3 py-1 rounded bg-blue-500 text-white"
                            data-type="bar">Bar</button>
                        <button class="chart-type-btn px-3 py-1 rounded bg-gray-200 text-gray-700"
                            data-type="line">Line</button>
                    </div>
                </div>
                <div class="h-72">
                    <canvas id="postsChart"></canvas>
                </div>
            </div>

            <!-- Add Real-time Activity Feed -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Live Feed -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Live Activity</h3>
                    <div class="space-y-4" id="live-feed">
                        @foreach($recentActivities as $activity)
                            <div class="flex items-center p-4 bg-gray-50 rounded-lg animate-fade-in">
                                <div class="flex-shrink-0">
                                    @switch($activity->type)
                                        @case('post_created')
                                            <span class="inline-block h-2 w-2 rounded-full bg-green-400"></span>
                                            @break
                                        @case('post_updated')
                                            <span class="inline-block h-2 w-2 rounded-full bg-blue-400"></span>
                                            @break
                                        @case('post_deleted')
                                            <span class="inline-block h-2 w-2 rounded-full bg-red-400"></span>
                                            @break
                                        @default
                                            <span class="inline-block h-2 w-2 rounded-full bg-gray-400"></span>
                                    @endswitch
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $activity->description }}</p>
                                    <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">System Metrics</h3>
                    <div class="space-y-4">
                        <!-- CPU Usage -->
                        <div class="relative pt-1">
                            <div class="flex mb-2 items-center justify-between">
                                <div>
                                    <span
                                        class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                                        CPU Usage
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-semibold inline-block text-blue-600">
                                        {{ $systemMetrics['cpu'] }}%
                                    </span>
                                </div>
                            </div>
                            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
                                <div style="width:{{ $systemMetrics['cpu'] }}%"
                                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Memory Usage -->
                        <div class="relative pt-1">
                            <div class="flex mb-2 items-center justify-between">
                                <div>
                                    <span
                                        class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-200">
                                        Memory Usage
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-semibold inline-block text-green-600">
                                        {{ $systemMetrics['memory'] }}%
                                    </span>
                                </div>
                            </div>
                            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-green-200">
                                <div style="width:{{ $systemMetrics['memory'] }}%"
                                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500">
                                </div>
                            </div>
                        </div>

                        <!-- Disk Usage -->
                        <div class="relative pt-1">
                            <div class="flex mb-2 items-center justify-between">
                                <div>
                                    <span
                                        class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-yellow-600 bg-yellow-200">
                                        Disk Usage
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-semibold inline-block text-yellow-600">
                                        {{ $systemMetrics['disk'] }}%
                                    </span>
                                </div>
                            </div>
                            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-yellow-200">
                                <div style="width:{{ $systemMetrics['disk'] }}%"
                                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-yellow-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('postsChart');
            let postsChart;

            if (canvas) {
                const ctx = canvas.getContext('2d');
                postsChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartData['labels']),
                        datasets: [{
                            label: 'Posts Published',
                            data: @json($chartData['data']),
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1,
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    font: {
                                        family: 'Figtree'
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        family: 'Figtree'
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    font: {
                                        family: 'Figtree',
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleFont: {
                                    family: 'Figtree',
                                    size: 13
                                },
                                bodyFont: {
                                    family: 'Figtree',
                                    size: 12
                                },
                                padding: 12,
                                cornerRadius: 8
                            }
                        }
                    }
                });

                // Handle chart type switching
                document.querySelectorAll('.chart-type-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        // Update button styles
                        document.querySelectorAll('.chart-type-btn').forEach(btn => {
                            btn.classList.remove('bg-blue-500', 'text-white');
                            btn.classList.add('bg-gray-200', 'text-gray-700');
                        });
                        this.classList.remove('bg-gray-200', 'text-gray-700');
                        this.classList.add('bg-blue-500', 'text-white');

                        // Update chart type
                        postsChart.config.type = this.dataset.type;
                        postsChart.update();
                    });
                });
            }
        });
    </script>
</x-app-layout>