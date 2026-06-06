<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-blue-500 leading-tight">
            {{ __('Raporty i Analityka') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-8 text-gray-900 dark:text-gray-100 relative">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-green-400 to-blue-500"></div>
                    
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <div class="w-full md:w-1/2 h-80">
                            <canvas id="expensesChart"></canvas>
                        </div>

                        <div class="w-full md:w-1/2">
                            <h3 class="text-2xl font-bold mb-4 tracking-tight text-gray-900 dark:text-white">Gdzie uciekają pieniądze?</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">
                                Poniższy wykres przedstawia 5 największych kategorii Twoich wydatków z zaimportowanego pliku.
                            </p>
                            <div class="space-y-3">
                                @foreach($chartStats as $stat)
                                    <div class="flex justify-between items-center text-sm border-b border-gray-100 dark:border-gray-700 pb-2">
                                        <span class="font-bold text-gray-700 dark:text-gray-200">{{ $stat->counterparty }}</span>
                                        <span class="font-extrabold text-red-500 dark:text-red-400">{{ number_format($stat->total, 2) }} PLN</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('expensesChart').getContext('2d');
            
            const labels = {!! json_encode($chartStats->pluck('counterparty')) !!};
            const dataValues = {!! json_encode($chartStats->pluck('total')) !!};

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: dataValues,
                        backgroundColor: ['#6366f1', '#a855f7', '#ec4899', '#f43f5e', '#f59e0b'],
                        borderWidth: 0,
                        hoverOffset: 20
                    }]
                },
                options: {
                    cutout: '70%',
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            // Drobny tuning dymka, który pojawia się po najechaniu na wykres
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            padding: 12,
                            cornerRadius: 8
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
</x-app-layout>