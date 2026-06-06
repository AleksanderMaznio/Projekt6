<?php
use App\Models\Subscription; 
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImportController;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    // 1. Pobieramy wszystkie transakcje
    $transactions = App\Models\Transaction::where('user_id', auth()->id())
                        ->orderBy('transaction_date', 'desc')
                        ->get();

    // 2. Pobieramy aktywne subskrypcje
    $subscriptions = Subscription::where('user_id', auth()->id())
                        ->where('is_active', true)
                        ->get();

    // 3. Wysyłamy obie zmienne do widoku
    return view('dashboard', compact('transactions', 'subscriptions'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Domyślne ścieżki profilu z Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // NASZE ŚCIEŻKI DO IMPORTU (chronione logowaniem)
    Route::get('/import', function () {
        return view('import');
    });
    Route::post('/import', [ImportController::class, 'store']);
});

require __DIR__.'/auth.php';