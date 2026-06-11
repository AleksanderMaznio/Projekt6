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

Route::get('/dashboard', function () {
    $userId = auth()->id();

    
    $transactions = Transaction::where('user_id', $userId)
                        ->orderBy('transaction_date', 'desc')
                        ->get();

   
    if ($transactions->isEmpty()) {
        $transactions = Transaction::whereNull('user_id')
                            ->orWhere('user_id', 0)
                            ->orderBy('transaction_date', 'desc')
                            ->get();
        
        $transactionIds = $transactions->pluck('id')->toArray();

       
        $subscriptions = Transaction::whereIn('id', $transactionIds)
                            ->when(Schema::hasColumn('transactions', 'is_subscription'), function ($query) {
                                return $query->where('is_subscription', true);
                            })
                            ->orderBy('transaction_date', 'desc')
                            ->get();

        
        $chartStats = Transaction::whereIn('id', $transactionIds)
                            ->select('contractor as counterparty', DB::raw('SUM(ABS(amount)) as total'))
                            ->groupBy('contractor')
                            ->orderBy('total', 'desc')
                            ->get();
    } else {
      
        
       
        $subscriptions = Transaction::where('user_id', $userId)
                            ->when(Schema::hasColumn('transactions', 'is_subscription'), function ($query) {
                                return $query->where('is_subscription', true);
                            })
                            ->orderBy('transaction_date', 'desc')
                            ->get();

        
        $chartStats = Transaction::where('user_id', $userId)
                            ->select('contractor as counterparty', DB::raw('SUM(ABS(amount)) as total'))
                            ->groupBy('contractor')
                            ->orderBy('total', 'desc')
                            ->get();
    }


    if ($chartStats->sum('total') == 0) {
        $chartStats = collect();
    }

    return view('dashboard', compact('transactions', 'subscriptions', 'chartStats'));
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::match(['get', 'post'], '/analytics', [ProfileController::class, 'analytics'])->name('analytics');
    Route::post('/analytics/cancel-subscription', [ProfileController::class, 'cancelSubscription'])->name('subscription.cancel');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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