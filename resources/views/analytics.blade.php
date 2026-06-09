<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analityka Finansowa & Zarządzanie') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Komunikat po pomyślnym usunięciu subskrypcji lub zmianie statusu --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl shadow-sm text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- KOLUMNY: WYKRES I FILTRY --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- DYNAMICZNY WYKRES SŁUPKOWY --}}
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Top 5 Największych Wydatków</h3>
                        @if($chartStats->isEmpty())
                            <p class="text-gray-500 text-sm">Brak danych do wyświetlenia wykresu.</p>
                        @else
                            <div class="space-y-3">
                                @php $maxTotal = $chartStats->max('total') ?? 1; @endphp
                                @foreach($chartStats as $stat)
                                    <div>
                                        <div class="flex justify-between text-sm font-medium text-gray-700 mb-1">
                                            <span>{{ $stat->counterparty ?? 'Nieznany odbiorca' }}</span>
                                            <span class="font-bold">{{ number_format($stat->total, 2, ',', ' ') }} zł</span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-3">
                                            <div class="bg-indigo-600 h-3 rounded-full transition-all duration-500" 
                                                 style="width: {{ ($stat->total / $maxTotal) * 100 }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- ODCHUDZONE I ELEGANCKIE FILTROWANIE --}}
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                        @if(is_null($premiumStats))
                            <div class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-10 text-center p-4">
                                <div>
                                    <span class="text-xs bg-amber-100 text-amber-800 font-bold px-2.5 py-1 rounded-md border border-amber-200">PRO</span>
                                    <h4 class="text-base font-bold text-gray-900 mt-2">Sortowanie i filtrowanie jak w banku</h4>
                                    <p class="text-xs text-gray-500 max-w-xs mt-1">Odblokuj zaawansowane przeszukiwanie historii finansowej.</p>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Filtrowanie historii operacji</h3>
                        </div>

                        <form method="GET" action="{{ route('analytics') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                            {{-- Szukaj nazwy z ikoną lupy --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Szukaj nazwy</label>
                                <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="np. Netflix..." 
                                        class="w-full text-sm pl-8 pr-3 py-1.5 border-gray-200 rounded-xl bg-white text-gray-800 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                        <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Data od --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Data od</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                    class="w-full text-sm px-3 py-1.5 border-gray-200 rounded-xl focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Data do --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Data do</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                    class="w-full text-sm px-3 py-1.5 border-gray-200 rounded-xl focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Skonsolidowane sortowanie (Kolumna + Kierunek w jednym!) --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Sortuj według</label>
                                <select name="sort_composite" class="w-full text-sm px-3 py-1.5 border-gray-200 rounded-xl focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="transaction_date_desc" {{ request('sort_composite', 'transaction_date_desc') == 'transaction_date_desc' ? 'selected' : '' }}>Daty: od najnowszych</option>
                                    <option value="transaction_date_asc" {{ request('sort_composite') == 'transaction_date_asc' ? 'selected' : '' }}>Daty: od najstarszych</option>
                                    <option value="amount_desc" {{ request('sort_composite') == 'amount_desc' ? 'selected' : '' }}>Kwoty: malejąco</option>
                                    <option value="amount_asc" {{ request('sort_composite') == 'amount_asc' ? 'selected' : '' }}>Kwoty: rosnąco</option>
                                    <option value="counterparty_asc" {{ request('sort_composite') == 'counterparty_asc' ? 'selected' : '' }}>Kontrahenta: A-Z</option>
                                    <option value="counterparty_desc" {{ request('sort_composite') == 'counterparty_desc' ? 'selected' : '' }}>Kontrahenta: Z-A</option>
                                </select>
                            </div>

                            {{-- Przyciski: Zastosuj oraz Reset --}}
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 justify-center inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-colors duration-150">
                                    Filtruj
                                </button>
                                <a href="{{ route('analytics') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-colors duration-150" title="Wyczyść filtry">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 19.43L20 20M4 4h5v.582"></path>
                                    </svg>
                                </a>
                            </div>
                        </form>
                    </div>

                </div>

                {{-- PANEL KASOWANIA SUBKRYPCJI --}}
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                        @if(is_null($premiumStats))
                            <div class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-10 text-center p-4">
                                <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100">
                                    <span class="text-xs bg-amber-100 text-amber-800 font-bold px-2.5 py-1 rounded-md border border-amber-200">PRO</span>
                                    <h4 class="text-sm font-bold text-gray-900 mt-2">Symulator Rezygnacji</h4>
                                    <p class="text-xs text-gray-500 mt-1">Funkcja pozwala anulować subskrypcję i usunąć przyszłe obciążenia z bazy.</p>
                                </div>
                            </div>
                        @endif

                        <h3 class="text-lg font-bold text-gray-900 mb-1">Anuluj subskrypcję</h3>
                        <p class="text-xs text-gray-400 mb-4">Zrezygnuj z usługi, aby wykasować wpisy rozliczeniowe z historii bazy.</p>

                        <form method="POST" action="{{ route('subscription.cancel') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Wybierz subskrypcję</label>
                                <select name="counterparty" class="w-full text-sm border-gray-200 rounded-xl" required>
                                    @foreach($chartStats as $stat)
                                        <option value="{{ $stat->counterparty }}">{{ $stat->counterparty }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Od jakiej daty kasować</label>
                                <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="w-full text-sm border-gray-200 rounded-xl" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Okres rezygnacji</label>
                                <select name="months" class="w-full text-sm border-gray-200 rounded-xl" required>
                                    <option value="1">1 miesiąc</option>
                                    <option value="3">3 miesiące</option>
                                    <option value="6">6 miesięcy</option>
                                    <option value="12">1 rok (12 m)</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition shadow-md" onclick="return confirm('Czy na pewno chcesz usunąć te wpisy z bazy danych w celu rezygnacji?')">
                                Potwierdź rezygnację
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- TABELA TRANSAKCJI --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Lista operacji po przefiltrowaniu (Wydatki)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <th class="p-4">Data</th>
                                <th class="p-4">Kontrahent</th>
                                <th class="p-4">Tytuł</th>
                                <th class="p-4 text-right">Kwota</th>
                                <th class="p-4 text-center">Akcja</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($transactions as $tx)
                                <tr class="transition {{ $tx->is_subscription ? 'bg-indigo-50/50 hover:bg-indigo-50/80 font-medium' : 'hover:bg-gray-50' }}">
                                    <td class="p-4 text-gray-600">{{ $tx->transaction_date }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-semibold text-gray-950">{{ $tx->counterparty }}</span>
                                            @if($tx->is_subscription)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 border border-indigo-200 shadow-sm animate-pulse">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.253 8H18"></path>
                                                    </svg>
                                                    Subskrypcja
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-4 text-gray-500">{{ $tx->title }}</td>
                                    <td class="p-4 text-right font-bold text-red-600">
                                        {{ number_format($tx->amount, 2, ',', ' ') }} {{ $tx->currency }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <form method="POST" action="{{ route('transaction.toggle-subscription', $tx->id) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold transition shadow-sm border {{ $tx->is_subscription ? 'bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                                                {{ $tx->is_subscription ? '★ Odznacz suba' : '☆ Oznacz jako sub' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-400">Brak transakcji spełniających kryteria.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>