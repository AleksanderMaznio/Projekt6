<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Strona główna
Route::get('/', function () {
    return view('welcome');
});

// Tryb demonstracyjny dla gości
Route::get('/demo', function () {
    return view('demo');
})->name('demo');

// Główny panel użytkownika (Dashboard)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// Trasy dla użytkowników zalogowanych i zweryfikowanych (Analityka i Premium)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::match(['get', 'post'], '/analytics', [ProfileController::class, 'analytics'])->name('analytics');
    Route::get('/analytics/export-subscriptions', [ProfileController::class, 'exportSubscriptions'])->name('analytics.export-subscriptions');
    Route::post('/analytics/cancel-subscription', [ProfileController::class, 'cancelSubscription'])->name('subscription.cancel');
    
    // [CRUD: CREATE] Nowa trasa do ręcznego dodawania subskrypcji z poziomu modala
    Route::post('/subscriptions/store', [ProfileController::class, 'storeSubscription'])->name('subscription.store');
    
    // [CRUD: UPDATE] Nowa trasa do zapisywania zmian po edycji subskrypcji w modalu
    Route::put('/transactions/{id}', [ProfileController::class, 'updateSubscription'])->name('transaction.update');
});

// Trasy profilu i zarządzania danymi (Wymagane tylko logowanie)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/imports/{importedFile}', [ProfileController::class, 'destroyImportedFile'])->name('profile.imports.destroy');
    
    // [CRUD: DELETE/TOGGLE] Przełączanie statusu subskrypcji (używane przez przycisk X)
    Route::post('/analytics/toggle-subscription/{id}', [ProfileController::class, 'toggleSubscription'])->name('transaction.toggle-subscription');

    // Import plików CSV
    Route::get('/import', function () {
        return view('import');
    })->name('import.index');
    
    Route::post('/import', [ImportController::class, 'store'])->name('import.store');
});

// Panel Administratora (Zabezpieczony middlewarem admina)
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('users', AdminUserController::class);
});

require __DIR__.'/auth.php';