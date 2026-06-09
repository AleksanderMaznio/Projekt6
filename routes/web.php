<?php

use App\Models\Subscription; 
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImportController;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController as AdminUserController;

// 1. STRONA GŁÓWNA (Dla niezalogowanych)
Route::get('/', function () {
    return view('welcome');
});

// 2. DASHBOARD (Główny panel użytkownika)
Route::get('/dashboard', function () {
    // Pobieramy wszystkie transakcje zalogowanego użytkownika do historii transakcji
    $transactions = Transaction::where('user_id', auth()->id())
                        ->orderBy('transaction_date', 'desc')
                        ->get();

    // POPRAWKA: Pobieramy transakcje oznaczone jako subskrypcje (kwoty ujemne, z flagą is_subscription)
    $subscriptions = Transaction::where('user_id', auth()->id())
                        ->where('is_subscription', true)
                        ->where('amount', '<', 0)
                        ->orderBy('transaction_date', 'desc')
                        ->get();

    return view('dashboard', compact('transactions', 'subscriptions'));
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. ANALITYKA I ZARZĄDZANIE SUBSKRYPCJAMI (Przeniesione do ProfileController)
Route::middleware(['auth', 'verified'])->group(function () {
    // Trasa wyświetlania analityki, filtrów i sortowania
    Route::get('/analytics', [ProfileController::class, 'analytics'])->name('analytics');
    
    // Trasa POST obsługująca teoretyczną rezygnację z subskrypcji (kasowanie wierszy)
    Route::post('/analytics/cancel-subscription', [ProfileController::class, 'cancelSubscription'])->name('subscription.cancel');
});

// 4. TRASY ZALOGOWANEGO UŻYTKOWNIKA (Profil i Import)
Route::middleware('auth')->group(function () {
    // Profil z Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/analytics/toggle-subscription/{id}', [ProfileController::class, 'toggleSubscription'])->name('transaction.toggle-subscription');

    // Import CSV
    Route::get('/import', function () {
        return view('import');
    })->name('import.index');
    
    Route::post('/import', [ImportController::class, 'store'])->name('import.store');
});

// 5. PANEL ADMINISTRATORA
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('users', AdminUserController::class);
});

require __DIR__.'/auth.php';