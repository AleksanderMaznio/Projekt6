<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\Subscription;
use Carbon\Carbon;

class DetectSubscriptions extends Command
{
    // Komenda, którą będziesz wpisywał w terminalu
    protected $signature = 'subscriptions:detect {user_id}';
    protected $description = 'Skanuje historie uzytkownika i wykrywa subskrypcje';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $this->info("Rozpoczynam analize dla uzytkownika ID: {$userId}...");

        // 1. Wyciągamy wszystkie transakcje tego użytkownika i grupujemy je po odbiorcy
        $transactions = Transaction::where('user_id', $userId)
                                    ->where('amount', '<', 0) // interesują nas tylko wydatki
                                    ->orderBy('transaction_date', 'asc')
                                    ->get()
                                    ->groupBy('counterparty');

        $detectedCount = 0;

        // 2. Analizujemy każdego odbiorcę z osobna
        foreach ($transactions as $counterparty => $group) {
            
            // Jeśli płatność u tego gościa była tylko jedna, to nie ma szans na cykliczność - omijamy
            if ($group->count() < 2) {
                continue; 
            }

            // Odrzucamy popularne sklepy z palca (to można w przyszłości rozwinąć w całą osobną bazę słów wykluczonych)
            $ignoredKeywords = ['Biedronka', 'Żabka', 'Orlen'];
            if (\Str::contains($counterparty, $ignoredKeywords, true)) {
                continue;
            }

            // 3. Sprawdzamy odstępy czasowe między płatnościami
            $isSubscription = true;
            $avgAmount = 0;

            for ($i = 1; $i < $group->count(); $i++) {
                $prevDate = Carbon::parse($group[$i - 1]->transaction_date);
                $currDate = Carbon::parse($group[$i]->transaction_date);
                
                // Liczymy różnicę w dniach (szukamy przedziału miesięcznego: ok. 28-31 dni)
                $diffInDays = $prevDate->diffInDays($currDate);

                if ($diffInDays < 27 || $diffInDays > 32) {
                    $isSubscription = false;
                    break; 
                }
                
                $avgAmount += abs($group[$i]->amount);
            }

            // 4. Jeśli test czasowy przeszedł, zapisujemy to do naszej tabeli subskrypcji
            if ($isSubscription) {
                // Obliczamy uśrednioną kwotę (na wypadek groszowych wahań na kursach walut itp.)
                $avgAmount = $avgAmount / ($group->count() - 1);
                $lastTransaction = $group->last();

                // Zapisujemy, ale upewniamy się najpierw, czy już wcześniej tego nie dodaliśmy (żeby nie dublować)
                Subscription::updateOrCreate(
                    [
                        'user_id' => $userId, 
                        'name' => $counterparty // Zakładamy, że nadawca to nazwa usługi
                    ],
                    [
                        'price' => $avgAmount,
                        'currency' => $lastTransaction->currency,
                        'billing_cycle_days' => 30,
                        'next_billing_date' => Carbon::parse($lastTransaction->transaction_date)->addDays(30),
                        'is_active' => true,
                    ]
                );

                // Oznaczamy oryginalne transakcje flagą z bazy
                foreach ($group as $transaction) {
                    $transaction->update(['is_subscription' => true]);
                }

                $this->line("Wykryto nowa subskrypcje: <fg=green>{$counterparty}</> na kwote {$avgAmount} PLN");
                $detectedCount++;
            }
        }

        $this->info("Koniec! Lacznie wykryto: {$detectedCount} aktywnych subskrypcji.");
    }
}