<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 border-l-4 border-indigo-500">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-extrabold mb-4 text-indigo-400">Twoje Aktywne Subskrypcje</h3>
                    
                    @if(isset($subscriptions) && $subscriptions->isEmpty())
                        <p class="text-gray-500">System nie wykrył jeszcze żadnych powtarzalnych płatności.</p>
                    @elseif(isset($subscriptions))
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach($subscriptions as $sub)
                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700">
                                    <h4 class="font-bold text-lg uppercase tracking-wide">{{ $sub->name }}</h4>
                                    <p class="text-2xl font-extrabold mt-2 text-red-400">-{{ $sub->price }} {{ $sub->currency }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Cykl: co {{ $sub->billing_cycle_days }} dni</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                         <p class="text-red-500">Zmienna $subscriptions nie została przekazana z kontrolera.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <h3 class="text-lg font-bold mb-4">Twoja historia transakcji</h3>

                    @if($transactions->isEmpty())
                        <p>Brak danych. Przejdź do <a href="/import" class="text-blue-500 hover:text-blue-400 underline">importu CSV</a>, aby wgrać wyciąg.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="py-2 px-4 border-b dark:border-gray-600 text-left text-sm font-semibold">Data</th>
                                        <th class="py-2 px-4 border-b dark:border-gray-600 text-left text-sm font-semibold">Tytuł</th>
                                        <th class="py-2 px-4 border-b dark:border-gray-600 text-left text-sm font-semibold">Odbiorca/Nadawca</th>
                                        <th class="py-2 px-4 border-b dark:border-gray-600 text-right text-sm font-semibold">Kwota</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="py-2 px-4 border-b dark:border-gray-600 text-sm">{{ $transaction->transaction_date }}</td>
                                            <td class="py-2 px-4 border-b dark:border-gray-600 text-sm">{{ $transaction->title }}</td>
                                            <td class="py-2 px-4 border-b dark:border-gray-600 text-sm">{{ $transaction->counterparty }}</td>
                                            <td class="py-2 px-4 border-b dark:border-gray-600 text-right text-sm font-bold {{ $transaction->amount < 0 ? 'text-red-500' : 'text-green-500' }}">
                                                {{ $transaction->amount }} {{ $transaction->currency }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
            </div>
    </div>
</x-app-layout>