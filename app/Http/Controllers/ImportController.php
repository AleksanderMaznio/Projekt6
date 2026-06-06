<?php

namespace App\Http\Controllers;

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

        // 5. Zapisujemy transakcje w bazie
        foreach ($data as $row) {
            if (count($row) < 5) continue; 

            Transaction::create([
                'user_id' => auth()->id(), // <--- TUTAJ JEST POPRAWKA! Przypisuje do Twojego aktualnego konta
                'transaction_date' => $row[0],
                'amount' => $row[1],
                'currency' => $row[2],
                'title' => $row[3],
                'counterparty' => $row[4],
                'is_subscription' => false
            ]);
        }

        // 6. AUTOMATYCZNE PRZEKIEROWANIE: Wracamy na dashboard od razu po imporcie
        return redirect()->route('dashboard');
    }
}