<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImportController;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DashboardController;



Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');
// 1. STRONA GŁÓWNA (Dla niezalogowanych)
Route::get('/', function () {
    return view('welcome');
});

// NOWOŚĆ: Trasa do trybu demonstracyjnego (Publiczna, bez logowania)
Route::get('/demo', function () {
    return view('demo');
})->name('demo');

// 2. DASHBOARD (Główny panel użytkownika)
Route::get('/dashboard', function () {
    $userId = auth()->id();

    // 1. Pobieramy WSZYSTKIE transakcje zalogowanego użytkownika (Historia)
    $transactions = Transaction::where('user_id', $userId)
                        ->orderBy('transaction_date', 'desc')
                        ->get();

    // AUTOMATYCZNY FALLBACK: Jeśli zalogowany użytkownik nie ma przypisanych transakcji,
    // ładujemy dane demonstracyjne (user_id = null lub 0), aby panel od razu żył
    if ($transactions->isEmpty()) {
        $transactions = Transaction::whereNull('user_id')
                            ->orWhere('user_id', 0)
                            ->orderBy('transaction_date', 'desc')
                            ->get();
        
        $transactionIds = $transactions->pluck('id')->toArray();

        // Subskrypcje (Kafelki) - Pobieramy te oznaczone jako subskrypcje bez względu na znak kwoty
        $subscriptions = Transaction::whereIn('id', $transactionIds)
                            ->when(Schema::hasColumn('transactions', 'is_subscription'), function ($query) {
                                return $query->where('is_subscription', true);
                            })
                            ->orderBy('transaction_date', 'desc')
                            ->get();

        // Statystyki do wykresu struktury wydatków
        $chartStats = Transaction::whereIn('id', $transactionIds)
                            ->select('contractor as counterparty', DB::raw('SUM(ABS(amount)) as total'))
                            ->groupBy('contractor')
                            ->orderBy('total', 'desc')
                            ->get();
    } else {
        // Jeśli użytkownik posiada już własne, zaimportowane dane:
        
        // Subskrypcje (Kafelki)
        $subscriptions = Transaction::where('user_id', $userId)
                            ->when(Schema::hasColumn('transactions', 'is_subscription'), function ($query) {
                                return $query->where('is_subscription', true);
                            })
                            ->orderBy('transaction_date', 'desc')
                            ->get();

        // Statystyki do wykresu struktury wydatków
        $chartStats = Transaction::where('user_id', $userId)
                            ->select('contractor as counterparty', DB::raw('SUM(ABS(amount)) as total'))
                            ->groupBy('contractor')
                            ->orderBy('total', 'desc')
                            ->get();
    }

    // Dodatkowe zabezpieczenie dla widoku przed dzieleniem przez zero, jeśli suma wydatków wynosi 0
    if ($chartStats->sum('total') == 0) {
        $chartStats = collect();
    }

    return view('dashboard', compact('transactions', 'subscriptions', 'chartStats'));
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. ANALITYKA I ZARZĄDZANIE SUBSKRYPCJAMI (Przeniesione do ProfileController)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::match(['get', 'post'], '/analytics', [ProfileController::class, 'analytics'])->name('analytics');
    Route::post('/analytics/cancel-subscription', [ProfileController::class, 'cancelSubscription'])->name('subscription.cancel');
});

// 4. TRASY ZALOGOWANEGO UŻYTKOWNIKA (Profil i Import)
Route::middleware('auth')->group(function () {
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