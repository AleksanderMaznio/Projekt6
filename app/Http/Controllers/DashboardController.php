<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function index()
{
    $userId = auth()->id();

    // Pobieramy dane do wykresu
    $data = \App\Models\Transaction::where('user_id', $userId)
        ->where('amount', '<', 0)
        ->selectRaw("DATE_FORMAT(transaction_date, '%Y-%m') as month")
        ->selectRaw("SUM(ABS(amount)) as total_spent")
        ->selectRaw("SUM(CASE WHEN is_subscription = 0 THEN ABS(amount) ELSE 0 END) as spent_without_subs")
        ->groupBy('month')
        ->orderBy('month', 'ASC')
        ->get();

    $chartData = [
        'labels' => $data->pluck('month'),
        'total' => $data->pluck('total_spent'),
        'no_subs' => $data->pluck('spent_without_subs')
    ];

    // Pobieramy pozostałe dane
    $subscriptions = \App\Models\Transaction::where('user_id', $userId)->where('is_subscription', true)->get();
    $transactions = \App\Models\Transaction::where('user_id', $userId)->latest()->get();

    return view('dashboard', compact('chartData', 'subscriptions', 'transactions'));
}
}