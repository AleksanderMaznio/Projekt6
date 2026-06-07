<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-extrabold text-2xl text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-500 leading-tight">
                {{ __('Podsumowanie finansów') }}
            </h2>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <a href="{{ url('/import') }}" class="inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl shadow-sm transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Wgraj nowy CSV
                </a>

                <a href="{{ route('analytics') }}" class="inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-400 to-blue-500 hover:from-green-500 hover:to-blue-600 text-white font-bold rounded-xl shadow-md transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                    Analityka
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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-8 text-gray-900 dark:text-gray-100 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>

                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex justify-center items-center w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-500 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold tracking-tight">Aktywne Subskrypcje</h3>
                    </div>
                    
                    @if(isset($subscriptions) && $subscriptions->isEmpty())
                        <div class="text-center py-6">
                            <p class="text-gray-500 dark:text-gray-400 text-lg">System nie wykrył jeszcze żadnych powtarzalnych płatności.</p>
                        </div>
                    @elseif(isset($subscriptions))
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($subscriptions as $sub)
                                <div class="p-5 border border-gray-100 dark:border-gray-700 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-900 relative group transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                                    <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-bl-full rounded-tr-xl -z-10 transition-all group-hover:scale-150"></div>
                                    <h4 class="font-bold text-lg uppercase tracking-wider text-gray-800 dark:text-gray-200">{{ $sub->name }}</h4>
                                    <p class="text-3xl font-extrabold mt-3 text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-pink-500">-{{ $sub->price }} <span class="text-sm text-gray-400">{{ $sub->currency }}</span></p>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-2 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Cykl: co {{ $sub->billing_cycle_days }} dni
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="p-8 text-gray-900 dark:text-gray-100 relative">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-purple-500 to-pink-500"></div>

                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex justify-center items-center w-10 h-10 bg-purple-100 dark:bg-purple-900/50 text-purple-500 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold tracking-tight">Historia Transakcji</h3>
                    </div>

                    @if($transactions->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p class="text-gray-500 dark:text-gray-400 text-lg">Brak danych.</p>
                            <a href="/import" class="mt-4 inline-block px-6 py-2 bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 font-semibold rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                                Przejdź do importu CSV
                            </a>
                        </div>
                    @else
                        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white dark:bg-gray-800">
                                    <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                                        <tr>
                                            <th class="py-4 px-6 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data</th>
                                            <th class="py-4 px-6 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tytuł</th>
                                            <th class="py-4 px-6 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Odbiorca / Nadawca</th>
                                            <th class="py-4 px-6 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kwota</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($transactions as $transaction)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                                <td class="py-3 px-6 text-sm whitespace-nowrap text-gray-600 dark:text-gray-300 font-medium">{{ $transaction->transaction_date }}</td>
                                                <td class="py-3 px-6 text-sm text-gray-800 dark:text-gray-200">{{ $transaction->title }}</td>
                                                <td class="py-3 px-6 text-sm text-gray-800 dark:text-gray-200 font-semibold">
                                                    {{ $transaction->counterparty }}
                                                    @if($transaction->is_subscription)
                                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300">
                                                            Subskrypcja
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-6 text-right text-sm font-bold whitespace-nowrap {{ $transaction->amount < 0 ? 'text-red-500 dark:text-red-400' : 'text-green-500 dark:text-green-400' }}">
                                                    {{ $transaction->amount }} <span class="text-xs text-gray-500">{{ $transaction->currency }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>