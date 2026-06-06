<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        // 4. Dump & Die
       // Usuwamy pierwszy element tablicy (bo to nagłówki: Data, Kwota itp.)
        array_shift($data);

        // Przechodzimy przez każdy wiersz i zapisujemy do bazy
        foreach ($data as $row) {
            // Zabezpieczenie przed pustymi liniami na końcu pliku CSV
            if (count($row) < 5) continue; 

            \App\Models\Transaction::create([
                'user_id' => 1, // Na sztywno przypisujemy do naszego fejkowego usera
                'transaction_date' => $row[0],
                'amount' => $row[1],
                'currency' => $row[2],
                'title' => $row[3],
                'counterparty' => $row[4],
                'is_subscription' => false // Domyślnie na start
            ]);
        }

        return "Elegancko! Plik wczytany i zapisany w bazie!";
    }
}