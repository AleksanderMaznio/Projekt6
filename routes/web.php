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

Route::get('/', function () {
    return view('welcome');
});


Route::get('/demo', function () {
    return view('demo');
})->name('demo');



Route::middleware(['auth', 'verified'])->group(function () {
    Route::match(['get', 'post'], '/analytics', [ProfileController::class, 'analytics'])->name('analytics');
    Route::get('/analytics/export-subscriptions', [ProfileController::class, 'exportSubscriptions'])->name('analytics.export-subscriptions');
    Route::post('/analytics/cancel-subscription', [ProfileController::class, 'cancelSubscription'])->name('subscription.cancel');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/imports/{importedFile}', [ProfileController::class, 'destroyImportedFile'])->name('profile.imports.destroy');
    Route::post('/analytics/toggle-subscription/{id}', [ProfileController::class, 'toggleSubscription'])->name('transaction.toggle-subscription');

  
    Route::get('/import', function () {
        return view('import');
    })->name('import.index');
    
    Route::post('/import', [ImportController::class, 'store'])->name('import.store');
});


Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('users', AdminUserController::class);
});

require __DIR__.'/auth.php';