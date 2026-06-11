<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-extrabold text-2xl text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-500 leading-tight">
                {{ __('Podsumowanie finansów & Analityka') }}
            </h2>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <a href="{{ url('/import') }}" class="inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold rounded-xl shadow-sm transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Wgraj nowy CSV
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ url('/admin') }}" class="inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold rounded-xl shadow-md transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Panel Admina
                    </a>
                @endif
            </div>
        </div>
    </x-slot>
   
    {{-- Stan Alpine.js: Sprawdzamy czy parametr select_sub jest w adresie URL, aby automatycznie rozwinąć sekcję wykresu --}}
    <div class="py-6 bg-gray-50 dark:bg-gray-900 min-h-screen" x-data="{ openStats: {{ request('select_sub') ? 'true' : 'false' }}, openHistory: true }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
             
            {{-- Komunikat sukcesu --}}
            @if(session('success'))
                <div class="bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-2.5 rounded-xl shadow-sm text-xs font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            {{-- PANEL 1: WYKRES ANALIZY I SUBSKRYPCJI (ZWIJANY) --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm rounded-xl overflow-hidden transition-all duration-300">
                <div @click="openStats = !openStats" class="p-3.5 bg-gray-50/80 dark:bg-gray-900/40 flex items-center justify-between cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors select-none">
                    <div class="flex items-center gap-2">
                        <div class="flex justify-center items-center w-6 h-6 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-full">
                            <svg class="w-3.5 h-3.5 transition-transform duration-300" :class="openStats ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-extrabold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Wykres wydatków & Zarządzanie subskrypcjami</span>
                    </div>
                    <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-md" x-text="openStats ? '✕ Schowaj sekcję' : '⚙ Rozwiń sekcję'"></span>
                </div>

                <div x-show="openStats" x-collapse x-transition class="p-4 border-t border-gray-100 dark:border-gray-700" style="display: none;">
                    
                    {{-- SEKCJA DYNAMICZNA: WYKRES LINIOWY LUB BLOKADA PREMIUM --}}
                    <div class="mb-6">
                        @if($premiumStats)
                            <div class="bg-gray-50 dark:bg-gray-900/30 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                                <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Analiza wydatków w czasie (Historia & Prognoza)</h3>
                                <div style="position: relative; height: 320px; width: 100%;">
                                    <canvas id="myUltimatePremiumChart"></canvas>
                                </div>
                            </div>
                        @else
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 p-6 rounded-xl text-center border border-dashed border-gray-300 dark:border-gray-600">
                                <div class="text-3xl mb-2">🔒</div>
                                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 mb-1">Zaawansowana analityka i prognozy liniowe</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-3">
                                    Statystyczne prognozy wydatków na kolejne miesiące oraz zaawansowane osie czasu są dostępne wyłącznie dla użytkowników posiadających konto <span class="text-amber-500 font-bold">Premium</span>.
                                </p>
                                <a href="#" class="inline-block bg-amber-500 hover:bg-amber-600 text-white font-semibold px-4 py-1.5 rounded-lg transition shadow-sm text-xs">
                                    Uaktualnij do Premium
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        
                        {{-- TOP 5 NAJWIĘKSZYCH WYDATKÓW --}}
                        <div class="lg:col-span-2 relative overflow-hidden flex flex-col justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Top 5 Największych Wydatków (% pełnej kwoty)</h3>
                                @if($chartStats->isEmpty())
                                    <p class="text-gray-400 dark:text-gray-500 text-xs py-4 font-medium">Brak danych do wyświetlenia wykresu.</p>
                                @else
                                    <div class="space-y-2.5">
                                        @php 
                                            $totalSum = $chartStats->sum('total') ?? 1; 
                                        @endphp
                                        @foreach($chartStats as $stat)
                                            @php 
                                                $percentage = ($stat->total / $totalSum) * 100;
                                            @endphp
                                            <div class="space-y-0.5">
                                                <div class="flex justify-between text-xs font-semibold text-gray-900 dark:text-gray-100">
                                                    <span class="truncate max-w-[220px] sm:max-w-[350px]" title="{{ $stat->counterparty }}">{{ $stat->counterparty ?? 'Nieznany odbiorca' }}</span>
                                                    <span>
                                                        {{ number_format($stat->total, 2, ',', ' ') }} zł 
                                                        <span class="text-[10px] text-gray-500 dark:text-gray-400 font-bold">({{ number_format($percentage, 1) }}%)</span>
                                                    </span>
                                                </div>
                                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                                    <div class="bg-indigo-600 dark:bg-indigo-500 h-2 rounded-full transition-all duration-500" 
                                                         style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- ANULOWANIE SUBSKRYPCJI --}}
                        <div class="border-t lg:border-t-0 lg:border-l border-gray-100 dark:border-gray-700 pt-4 lg:pt-0 lg:pl-4 relative overflow-hidden flex flex-col justify-between">
                            @if(!$premiumStats)
                                <div class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-[1px] flex items-center justify-center z-10 text-center p-4 rounded-xl">
                                    <div class="bg-white dark:bg-gray-900 p-3 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                        <span class="text-[9px] bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-400 font-bold px-1.5 py-0.5 rounded border border-amber-200 dark:border-amber-800">PRO</span>
                                        <h4 class="text-xs font-bold text-gray-900 dark:text-gray-100 mt-1">Zarządzanie Subskrypcjami</h4>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Dostępne w pakiecie premium.</p>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="flex justify-center items-center w-6 h-6 bg-red-100 dark:bg-red-950/50 text-red-600 dark:text-red-400 rounded-full">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </div>
                                    <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Anuluj subskrypcję</h3>
                                </div>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-3 font-medium">Wykasuj przyszłe obciążenia z bazy danych.</p>

                                <form method="POST" action="{{ route('subscription.cancel') }}" class="space-y-2">
                                    @csrf
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight mb-0.5">Subskrypcja</label>
                                        <select name="counterparty" class="w-full text-xs py-1 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-medium focus:ring-1 focus:ring-red-500 focus:border-red-500" required>
                                            @foreach($chartStats as $stat)
                                                {{-- Automatyczne zaznaczanie opcji przekazanej z poziomu parametru URL --}}
                                                <option value="{{ $stat->counterparty }}" {{ request('select_sub') == $stat->counterparty ? 'selected' : '' }}>
                                                    {{ $stat->counterparty }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight mb-0.5">Data od</label>
                                            <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="w-full text-xs py-1 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-medium focus:ring-1 focus:ring-red-500 focus:border-red-500" required>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight mb-0.5">Okres</label>
                                            <select name="months" class="w-full text-xs py-1 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-medium focus:ring-1 focus:ring-red-500 focus:border-red-500" required>
                                                <option value="1">1 miesiąc</option>
                                                <option value="3">3 mieś.</option>
                                                <option value="6">6 mieś.</option>
                                                <option value="12">1 rok</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition shadow-sm mt-1" onclick="return confirm('Czy na pewno chcesz anulować subskrypcję?')">
                                        Potwierdź rezygnację
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- PANEL 2: CAŁA HISTORIA TRANSAKCJI WRAZ Z FILTRAMI (ZWIJANA) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                
                <div @click="openHistory = !openHistory" class="p-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/40 transition-colors select-none">
                    <div class="flex items-center gap-2">
                        <div class="flex justify-center items-center w-6 h-6 bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 rounded-full">
                            <svg class="w-3.5 h-3.5 transition-transform duration-300" :class="openHistory ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        </div>
                        <h3 class="text-xs font-extrabold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Historia Transakcji i Filtrowanie</h3>
                    </div>
                    <span class="text-[11px] font-bold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/60 px-2 py-0.5 rounded-md" x-text="openHistory ? '✕ Ukryj historię' : '👁 Pokaż historię'"></span>
                </div>

                <div x-show="openHistory" x-collapse x-transition>
                    
                    {{-- PASEK FILTRÓW --}}
                    <div class="p-4 bg-gray-50/50 dark:bg-gray-900/20 border-b border-gray-100 dark:border-gray-700">
                        <form method="GET" action="{{ route('analytics') }}" id="filterForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2 items-end bg-white dark:bg-gray-800 p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight mb-0.5">Nazwa operacji</label>
                                <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Szukaj..." 
                                        class="w-full text-xs pl-6 pr-2 py-1.5 border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-medium focus:bg-white dark:focus:bg-gray-900 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                        <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight mb-0.5">Data od</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                    class="w-full text-xs px-1.5 py-1 border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-medium focus:bg-white dark:focus:bg-gray-900 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight mb-0.5">Data do</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                    class="w-full text-xs px-1.5 py-1 border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-medium focus:bg-white dark:focus:bg-gray-900 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight mb-0.5">Sortowanie</label>
                                <select name="sort_composite" class="w-full text-xs px-1.5 py-1 border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-medium focus:bg-white dark:focus:bg-gray-900 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="transaction_date_desc" {{ request('sort_composite', 'transaction_date_desc') == 'transaction_date_desc' ? 'selected' : '' }}>Najnowsze</option>
                                    <option value="transaction_date_asc" {{ request('sort_composite') == 'transaction_date_asc' ? 'selected' : '' }}>Najstarsze</option>
                                    <option value="amount_desc" {{ request('sort_composite') == 'amount_desc' ? 'selected' : '' }}>Kwota: malejąco</option>
                                    <option value="amount_asc" {{ request('sort_composite') == 'amount_asc' ? 'selected' : '' }}>Kwota: rosnąco</option>
                                    <option value="counterparty_asc" {{ request('sort_composite') == 'counterparty_asc' ? 'selected' : '' }}>A-Z Kontrahent</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight mb-0.5">Na stronie</label>
                                <select name="per_page" onchange="document.getElementById('filterForm').submit();" class="w-full text-xs px-1.5 py-1 border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-medium focus:bg-white dark:focus:bg-gray-900 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 wpisów</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 wpisów</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 wpisów</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 wpisów</option>
                                </select>
                            </div>

                            <div class="flex gap-1.5">
                                <button type="submit" class="flex-1 justify-center inline-flex items-center px-2.5 py-1.5 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 text-white text-xs font-bold rounded transition-colors shadow-sm">
                                    Filtruj
                                </button>
                                <a href="{{ route('analytics') }}" class="inline-flex items-center justify-center w-8 h-8 bg-red-50 hover:bg-red-100 dark:bg-red-950/40 dark:hover:bg-red-900/60 text-red-600 dark:text-red-400 rounded-lg transition-colors border border-red-200 dark:border-red-900/60" title="Wyczyść filtry">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </div>
                        </form>
                    </div>

                    {{-- TABELA HISTORII --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-gray-900/30 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                                    <th class="px-4 py-2">Data</th>
                                    <th class="px-4 py-2">Kontrahent / Tytuł</th>
                                    <th class="px-4 py-2 text-right">Kwota</th>
                                    <th class="px-4 py-2 text-center">Status sub.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700 text-xs text-gray-800 dark:text-gray-200">
                                @forelse($transactions as $transaction)
                                    <tr class="transition {{ $transaction->is_subscription ? 'bg-indigo-50/10 dark:bg-indigo-950/20 hover:bg-indigo-50/30 font-semibold text-gray-900 dark:text-gray-100' : 'hover:bg-gray-50/50 dark:hover:bg-gray-700/30' }}">
                                        <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400 whitespace-nowrap font-medium">{{ $transaction->transaction_date }}</td>
                                        <td class="px-4 py-2.5">
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-bold text-gray-900 dark:text-gray-100">{{ $transaction->counterparty }}</span>
                                                    @if($transaction->is_subscription)
                                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-indigo-100 text-indigo-900 dark:bg-indigo-950 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                                            Subskrypcja
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-gray-500 dark:text-gray-400 text-[11px] mt-0.5 font-medium">{{ $transaction->title }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2.5 text-right font-extrabold whitespace-nowrap {{ $transaction->amount < 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                            <span>{{ $transaction->amount < 0 ? '-' : '+' }}{{ number_format(abs($transaction->amount), 2, ',', ' ') }}</span> 
                                            <span class="text-[10px] text-gray-500 font-normal">{{ $transaction->currency }}</span>
                                        </td>
                                        
                                        {{-- AKCJA Z DUŻYM CZERWONYM PRZYCISKIEM X (ZAMIAST STAREGO MARKERU SUB) --}}
                                        <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                            <form method="POST" action="{{ route('transaction.toggle-subscription', $transaction->id) }}">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-black transition shadow-sm border {{ $transaction->is_subscription ? 'bg-red-600 text-white border-red-600 hover:bg-red-700' : 'bg-white dark:bg-gray-900 text-gray-400 dark:text-gray-500 border-gray-200 dark:border-gray-700 hover:bg-red-50 hover:text-red-600 hover:border-red-200' }}"
                                                        title="{{ $transaction->is_subscription ? 'Usuń z subskrypcji' : 'Dodaj do subskrypcji' }}">
                                                    ✕
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400 text-xs font-medium">Brak transakcji spełniających kryteria.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PASEK PAGINACJI LARAVELA --}}
                    @if($transactions->hasPages())
                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700">
                            {{ $transactions->links() }}
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const canvas = document.getElementById('myUltimatePremiumChart');
        if (!canvas) {
            return; 
        }

        if (typeof Chart === 'undefined') {
            console.error("Błąd: Biblioteka Chart.js nie została prawidłowo załadowana.");
            return;
        }

        try {
            const ctx = canvas.getContext('2d');
            
            const rawData = {!! json_encode($chartData ?? [
                'labels' => [], 
                'total' => [], 
                'no_subs' => [], 
                'forecast_total' => [], 
                'forecast_no_subs' => []
            ]) !!};

            if (window.ultimateChartInstance) {
                window.ultimateChartInstance.destroy();
            }

            window.ultimateChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: rawData.labels,
                    datasets: [
                        {
                            label: 'Wydatki całkowite (Historia)',
                            data: rawData.total,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.05)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Wydatki całkowite (Prognoza)',
                            data: rawData.forecast_total,
                            borderColor: '#f97316',
                            borderDash: [6, 6],
                            borderWidth: 3,
                            pointRadius: 4,
                            tension: 0.4,
                            fill: false
                        },
                        {
                            label: 'Bez subskrypcji (Historia)',
                            data: rawData.no_subs,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.05)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Bez subskrypcji (Prognoza)',
                            data: rawData.forecast_no_subs,
                            borderColor: '#a855f7',
                            borderDash: [6, 6],
                            borderWidth: 3,
                            pointRadius: 4,
                            tension: 0.4,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        } catch (error) {
            console.error("Wystąpił błąd podczas inicjalizacji wykresu:", error);
        }
    });
    </script>
</x-app-layout>