<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
   public function index()
{
    $userId = auth()->id();

    // Pobieramy dane do wykresu
    $monthExpression = DB::getDriverName() === 'sqlite'
        ? "strftime('%Y-%m', transaction_date) as month"
        : "DATE_FORMAT(transaction_date, '%Y-%m') as month";

    $data = \App\Models\Transaction::where('user_id', $userId)
        ->where('amount', '<', 0)
        ->selectRaw($monthExpression)
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
    $subscriptions = \App\Models\Transaction::where('user_id', $userId)
        ->where('is_subscription', true)
        ->orderBy('transaction_date', 'desc')
        ->get()
        ->groupBy(function ($transaction) {
            return mb_strtolower(trim($transaction->counterparty ?? '')) . '::' . mb_strtolower(trim($transaction->title ?? ''));
        })
        ->map(function ($group) {
            $latest = $group->sortByDesc('transaction_date')->first();

            return (object) [
                'id' => $latest->id,
                'counterparty' => $latest->counterparty,
                'title' => $latest->title,
                'amount' => $latest->amount,
                'currency' => $latest->currency,
                'transaction_date' => $latest->transaction_date,
            ];
        })
        ->values();

    $transactions = \App\Models\Transaction::where('user_id', $userId)->latest()->get();

    return view('dashboard', compact('chartData', 'subscriptions', 'transactions'));
}
}