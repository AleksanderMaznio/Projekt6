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
    // Pobieramy wszystkie transakcje zalogowanego użytkownika
    $transactions = Transaction::where('user_id', auth()->id())
                        ->orderBy('transaction_date', 'desc')
                        ->get();

    // Pobieramy aktywne subskrypcje
    $subscriptions = Subscription::where('user_id', auth()->id())
                        ->where('is_active', true)
                        ->get();

    return view('dashboard', compact('transactions', 'subscriptions'));
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. ANALITYKA (Z rozróżnieniem na Premium - TYLKO JEDNA TRASA)
Route::get('/analytics', function () {
    $user = auth()->user();
    $userId = $user->id;

    // Podstawowe dane (Dla każdego użytkownika)
    $chartStats = Transaction::where('user_id', $userId)
        ->where('amount', '<', 0)
        ->selectRaw('counterparty, ABS(SUM(amount)) as total')
        ->groupBy('counterparty')
        ->orderByDesc('total')
        ->take(5)
        ->get();

    // Dodatkowe dane (Tylko dla Premium i Admina)
    $premiumStats = null;
    
    if ($user->isPremium() || $user->isAdmin()) {
        $monthlyExpenses = Transaction::where('user_id', $userId)
            ->where('amount', '<', 0)
            ->selectRaw("strftime('%Y-%m', transaction_date) as month, ABS(SUM(amount)) as total")
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $averageExpense = Transaction::where('user_id', $userId)
            ->where('amount', '<', 0)
            ->avg('amount');

        $premiumStats = [
            'monthly_expenses' => $monthlyExpenses,
            'average_expense'  => abs($averageExpense ?? 0),
        ];
    }

    return view('analytics', compact('chartStats', 'premiumStats'));
})->middleware(['auth', 'verified'])->name('analytics');

// 4. TRASY ZALOGOWANEGO UŻYTKOWNIKA (Profil i Import)
Route::middleware('auth')->group(function () {
    // Profil z Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Import CSV
    Route::get('/import', function () {
        return view('import');
    })->name('import.index'); // warto dodać name
    
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