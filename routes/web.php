<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/import', function () {
    return view('import');
});
use App\Http\Controllers\ImportController;

Route::get('/import', function () {
    return view('import');
});

// To odbierze plik po kliknięciu przycisku
Route::post('/import', [ImportController::class, 'store']);