<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SubTracker - Tryb Demonstracyjny</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 antialiased" x-data="demoApp()">

    <div class="bg-gradient-to-r from-amber-500 to-orange-600 text-white text-center py-2.5 px-4 text-sm font-semibold shadow-sm flex justify-between items-center">
        <div class="mx-auto flex items-center gap-2">
            <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Jesteś w trybie demonstracyjnym. Możesz testować funkcje bez zakładania konta!
        </div>
        <a href="/register" class="bg-white text-orange-600 px-3 py-1 rounded-lg font-bold text-xs hover:bg-orange-50 transition-colors">Załóż konto</a>
    </div>

    <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <span class="font-black text-xl text-indigo-600 dark:text-indigo-400 tracking-wider">SubTracker<span class="text-xs font-normal text-gray-400 ml-1">DEMO</span></span>
                <div class="flex gap-4 text-sm font-medium">
                    <button @click="currentTab = 'panel'" :class="currentTab === 'panel' ? 'text-indigo-600 dark:text-indigo-400 font-bold border-b-2 border-indigo-500 pb-5 pt-5' : 'text-gray-500 hover:text-gray-700 pb-5 pt-5'">Mój Panel</button>
                    <button @click="currentTab = 'charts'" :class="currentTab === 'charts' ? 'text-indigo-600 dark:text-indigo-400 font-bold border-b-2 border-indigo-500 pb-5 pt-5' : 'text-gray-500 hover:text-gray-700 pb-5 pt-5'">Analityka wydatków</button>
                </div>
            </div>
            <div class="flex gap-2">
                <button @click="resetDemo()" class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-3 py-2 rounded-xl font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">Resetuj dane</button>
                <button @click="simulateCsvImport()" class="text-xs bg-indigo-600 text-white px-4 py-2 rounded-xl font-bold hover:bg-indigo-700 shadow-sm transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Wgraj testowy plik (.csv)
                </button>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div x-show="currentTab === 'panel'" class="space-y-8" x-transition>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-8 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-extrabold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                        <span class="w-2 h-6 bg-indigo-500 rounded-full"></span> Zidentyfikowane subskrypcje
                    </h3>
                    <span class="text-xs text-gray-400">Wykryte automatycznie z historii</span>
                </div>

                <template x-if="subscriptions.length === 0">
                    <div class="text-center py-12 border-2 border-dashed border-gray-100 dark:border-gray-700 rounded-xl">
                        <p class="text-gray-400">Brak danych. Kliknij <strong class="text-indigo-500 cursor-pointer hover:underline" @click="simulateCsvImport()">Wgraj testowy plik</strong> na górze, aby zasymulować działanie algorytmu.</p>
                    </div>
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="(sub, index) in subscriptions" :key="index">
                        <div class="p-5 border border-gray-100 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900 relative group transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                            <h4 class="font-bold text-lg text-gray-800 dark:text-gray-200" x-text="sub.name"></h4>
                            <p class="text-2xl font-black mt-2 text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-pink-500">
                                -<span x-text="sub.price"></span> <span class="text-xs text-gray-400">PLN</span>
                            </p>
                            <div class="mt-3 flex justify-between items-center text-xs text-gray-500">
                                <span x-text="'Cykl: co ' + sub.cycle + ' dni'"></span>
                                <button @click="deleteSub(index)" class="text-red-400 hover:text-red-600 font-medium">Usuń testowo</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-8 shadow-sm">
                <h3 class="text-xl font-extrabold text-gray-800 dark:text-gray-200 mb-6">Pełny wyciąg operacji bazy demonstracyjnej</h3>
                <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Tytuł operacji</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Odbiorca</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase">Kwota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-800">
                            <template x-for="tx in transactions">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-500" x-text="tx.date"></td>
                                    <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200" x-text="tx.title"></td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        <span x-text="tx.counterparty"></span>
                                        <template x-if="tx.is_sub">
                                            <span class="ml-2 px-2 py-0.5 rounded text-xxs font-bold bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600">SUBSKRYPCJA</span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-right" :class="tx.amount < 0 ? 'text-red-500' : 'text-green-500'" x-text="tx.amount + ' PLN'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="currentTab === 'charts'" class="space-y-8" x-init="$watch('transactions', () => renderChart())" x-transition>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-8 shadow-sm">
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="w-full md:w-1/2 h-80 relative flex justify-center items-center">
                        <canvas id="demoChart"></canvas>
                    </div>
                    <div class="w-full md:w-1/2">
                        <h3 class="text-2xl font-bold mb-2 text-gray-800 dark:text-white">Struktura największych kosztów</h3>
                        <p class="text-sm text-gray-400 mb-6">Wykres kołowy generowany w czasie rzeczywistym z zasymulowanej tabeli transakcji.</p>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm pb-2 border-b dark:border-gray-700"><span class="font-bold text-gray-600 dark:text-gray-300">CityFit</span><span class="text-red-500 font-extrabold">139.00 PLN</span></div>
                            <div class="flex justify-between text-sm pb-2 border-b dark:border-gray-700"><span class="font-bold text-gray-600 dark:text-gray-300">Xbox Game Pass</span><span class="text-red-500 font-extrabold">49.99 PLN</span></div>
                            <div class="flex justify-between text-sm pb-2 border-b dark:border-gray-700"><span class="font-bold text-gray-600 dark:text-gray-300">Netflix</span><span class="text-red-500 font-extrabold">43.00 PLN</span></div>
                            <div class="flex justify-between text-sm pb-2 border-b dark:border-gray-700"><span class="font-bold text-gray-600 dark:text-gray-300">Disney+</span><span class="text-red-500 font-extrabold">29.99 PLN</span></div>
                            <div class="flex justify-between text-sm pb-2 border-b dark:border-gray-700"><span class="font-bold text-gray-600 dark:text-gray-300">Spotify</span><span class="text-red-500 font-extrabold">19.99 PLN</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function demoApp() {
            return {
                currentTab: 'panel',
                subscriptions: [],
                transactions: [],
                chartInstance: null,

                init() {
                    
                    this.resetDemo();
                },

                resetDemo() {
                    this.subscriptions = [];
                    this.transactions = [
                        { date: '2026-06-10', title: 'Wypłata czerwiec', counterparty: 'Pracodawca Sp. z o.o.', amount: 3500.00, is_sub: false },
                        { date: '2026-06-05', title: 'Zakupy spożywcze', counterparty: 'Biedronka', amount: -154.20, is_sub: false },
                        { date: '2026-06-01', title: 'Paliwo', counterparty: 'Orlen', amount: -210.00, is_sub: false },
                    ];
                    this.renderChart();
                },

                simulateCsvImport() {
                    
                    this.transactions = [
                        { date: '2026-06-10', title: 'Wypłata czerwiec', counterparty: 'Pracodawca Sp. z o.o.', amount: 3500.00, is_sub: false },
                        { date: '2026-06-06', title: 'Opłata członkowska', counterparty: 'CityFit', amount: -139.00, is_sub: true },
                        { date: '2026-06-04', title: 'Abonament premium', counterparty: 'Xbox Game Pass', amount: -49.99, is_sub: true },
                        { date: '2026-06-03', title: 'Subskrypcja VOD', counterparty: 'NETFLIX.COM', amount: -43.00, is_sub: true },
                        { date: '2026-06-02', title: 'Abonament rodzinny', counterparty: 'Disney+', amount: -29.99, is_sub: true },
                        { date: '2026-06-01', title: 'Muzyka premium', counterparty: 'Spotify AB', amount: -19.99, is_sub: true },
                        { date: '2026-05-06', title: 'Opłata członkowska', counterparty: 'CityFit', amount: -139.00, is_sub: true },
                        { date: '2026-05-03', title: 'Subskrypcja VOD', counterparty: 'NETFLIX.COM', amount: -43.00, is_sub: true },
                    ];

                    this.subscriptions = [
                        { name: 'CityFit', price: '139.00', cycle: 30 },
                        { name: 'Xbox Game Pass', price: '49.99', cycle: 30 },
                        { name: 'Netflix', price: '43.00', cycle: 30 },
                        { name: 'Disney+', price: '29.99', cycle: 30 },
                        { name: 'Spotify AB', price: '19.99', cycle: 30 },
                    ];
                    
                    setTimeout(() => this.renderChart(), 50);
                },

                deleteSub(index) {
                    this.subscriptions.splice(index, 1);
                },

                renderChart() {
                    const ctx = document.getElementById('demoChart');
                    if (!ctx) return;

                    if (this.chartInstance) {
                        this.chartInstance.destroy();
                    }

                   
                    const hasData = this.subscriptions.length > 0;
                    const labels = hasData ? this.subscriptions.map(s => s.name) : ['Biedronka', 'Orlen'];
                    const dataValues = hasData ? this.subscriptions.map(s => parseFloat(s.price)) : [154.20, 210.00];

                    this.chartInstance = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: dataValues,
                                backgroundColor: ['#6366f1', '#a855f7', '#ec4899', '#f43f5e', '#f59e0b'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            cutout: '72%',
                            plugins: { legend: { display: false } },
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>