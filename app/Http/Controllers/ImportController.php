<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use App\Models\Transaction;

class ImportController extends Controller
{
    public function store(Request $request)
    {
        // 1. Walidacja
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048'
        ]);

        // 2. Pobieramy plik
        $file = $request->file('csv_file');

        // 3. Otwieramy plik i mapujemy go wiersz po wierszu
        $data = array_map('str_getcsv', file($file->getRealPath()));

        // 4. Usuwamy nagłówki (Data, Kwota itp.)
        array_shift($data);

        // 5. Zapisujemy transakcje w bazie z zabezpieczeniem przed duplikatami
        foreach ($data as $row) {
            if (count($row) < 5) continue; 

            // Używamy firstOrCreate, aby nie dodawać drugi raz tego samego przelewu
            Transaction::firstOrCreate(
                [
                    // Szukamy, czy istnieje transakcja dla tego usera, w tym dniu, na tę kwotę i z tym tytułem
                    'user_id' => auth()->id(),
                    'transaction_date' => $row[0],
                    'amount' => $row[1],
                    'title' => $row[3]
                ],
                [
                    // Jeśli nie istnieje, uzupełniamy brakujące dane i tworzymy nowy rekord w bazie
                    'currency' => $row[2],
                    'counterparty' => $row[4],
                    'is_subscription' => false
                ]
            );
        }

        // 6. Odpalamy skrypt wykrywający subskrypcje w tle
        Artisan::call('subscriptions:detect', [
            'user_id' => auth()->id()
        ]);

        // 7. AUTOMATYCZNE PRZEKIEROWANIE: Wracamy na dashboard od razu po imporcie
        return redirect()->route('dashboard');
    }
}