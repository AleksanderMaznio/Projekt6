<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            Podgląd danych użytkownika: <span class="text-indigo-600">{{ $user->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Informacje o użytkowniku --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm mb-6 border border-gray-100 dark:border-gray-700">
                <p class="text-sm text-gray-600 dark:text-gray-400">Email: <strong>{{ $user->email }}</strong></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Status: 
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $user->isPremium() ? 'bg-amber-100 text-amber-800' : 'bg-gray-100' }}">
                        {{ $user->isPremium() ? 'PREMIUM' : 'FREE' }}
                    </span>
                </p>
            </div>

            {{-- TABELA TRANSAKCJI (Styl identyczny jak u użytkownika) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                <div class="mb-6">
    <a href="{{ route('admin.users.index') }}" 
       class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-bold rounded-lg transition-all shadow-sm">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Wróć do listy użytkowników
    </a>
</div>   
                <h3 class="text-xs font-extrabold text-gray-800 dark:text-gray-200 uppercase tracking-wider">
                        Historia Transakcji Użytkownika
                    </h3>
                    
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/30 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                                <th class="px-4 py-2">Data</th>
                                <th class="px-4 py-2">Kontrahent / Tytuł</th>
                                <th class="px-4 py-2 text-right">Kwota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700 text-xs text-gray-800 dark:text-gray-200">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $transaction->transaction_date }}</td>
                                    <td class="px-4 py-2.5">
                                        <div class="font-bold text-gray-900 dark:text-gray-100">{{ $transaction->counterparty }}</div>
                                        <div class="text-gray-500 dark:text-gray-400 text-[11px]">{{ $transaction->title }}</div>
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-extrabold {{ $transaction->amount < 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $transaction->amount < 0 ? '-' : '+' }}{{ number_format(abs($transaction->amount), 2, ',', ' ') }} zł
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-gray-500">Brak transakcji dla tego użytkownika.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>