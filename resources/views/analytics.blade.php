<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analityka Finansowa') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Top 5 Największych Wydatków</h3>
                
                @if($chartStats->isEmpty())
                    <p class="text-gray-500 text-sm">Brak danych do wyświetlenia wykresu.</p>
                @else
                    <div class="space-y-3">
                        @foreach($chartStats as $stat)
                            <div>
                                <div class="flex justify-between text-sm font-medium text-gray-700 mb-1">
                                    <span>{{ $stat->counterparty ?? 'Nieznany odbiorca' }}</span>
                                    <span class="font-bold">{{ number_format($stat->total, 2, ',', ' ') }} zł</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-3">
                                    <div class="bg-indigo-600 h-3 rounded-full" style="width: 70%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden relative">
                
                @if(is_null($premiumStats))
                    <div class="absolute inset-0 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center z-10 p-4 text-center">
                        <div class="bg-indigo-50 p-3 rounded-full text-indigo-600 mb-3 shadow-md">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900">Odblokuj Statystyki Premium</h4>
                        <p class="text-gray-500 max-w-sm mt-1 text-sm">Uzyskaj dostęp do historii trendów miesięcznych i wyliczeń średnich wydatków.</p>
                        <span class="mt-3 text-xs bg-indigo-600 text-white px-4 py-1.5 rounded-xl font-semibold shadow-sm">
                            Napisz do Admina o dostęp
                        </span>
                    </div>
                @endif

                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">Miesięczne Trendy i Średnie</h3>
                    <span class="text-xs bg-amber-100 text-amber-800 font-bold px-2.5 py-1 rounded-md border border-amber-200">PRO</span>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 bg-white rounded-xl border border-gray-200">
                        <h4 class="text-sm font-medium text-gray-500">Średni pojedynczy wydatek</h4>
                        <p class="text-3xl font-extrabold text-indigo-600 mt-2">
                            {{ number_format($premiumStats['average_expense'] ?? 0, 2, ',', ' ') }} zł
                        </p>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-gray-200">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Historia wydatków m/m</h4>
                        @if(isset($premiumStats['monthly_expenses']))
                            <div class="space-y-1">
                                @foreach($premiumStats['monthly_expenses'] as $monthly)
                                    <div class="flex justify-between text-xs text-gray-600">
                                        <span>{{ $monthly->month }}</span>
                                        <span class="font-semibold">{{ number_format($monthly->total, 2, ',', ' ') }} zł</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="h-12 bg-gray-100 animate-pulse rounded"></div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>