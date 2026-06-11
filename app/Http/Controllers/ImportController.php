<?php

namespace App\Http\Controllers;

use App\Models\ImportedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use App\Models\Transaction;

class ImportController extends Controller
{
    public function store(Request $request)
    {
      
        $request->validate([
            'csv_file' => 'required|file|mimetypes:text/csv|max:2048'
        ]);

        // 2. Pobieramy plik
        $file = $request->file('csv_file');

        $importedFile = ImportedFile::create([
            'user_id' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
        ]);

       
        $data = array_map('str_getcsv', file($file->getRealPath()));
        array_shift($data);

    
        foreach ($data as $row) {
            if (count($row) < 5) continue; 

        
            Transaction::firstOrCreate(
                [
                    'user_id' => auth()->id(),
                    'transaction_date' => $row[0],
                    'amount' => $row[1],
                    'title' => $row[3]
                ],
                [
                    'currency' => $row[2],
                    'counterparty' => $row[4],
                    'is_subscription' => false,
                    'imported_file_id' => $importedFile->id,
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