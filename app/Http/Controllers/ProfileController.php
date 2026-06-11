<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ImportedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use App\Models\Transaction; 
use Carbon\Carbon;          
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'importedFiles' => $request->user()->importedFiles()->latest()->get(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
            if ($request->user()->avatar) {
                Storage::disk('public')->delete($request->user()->avatar);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $request->user()->avatar = $path;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Usuwanie zaimportowanego pliku CSV wraz z jego transakcjami.
     */
    public function destroyImportedFile(ImportedFile $importedFile): RedirectResponse
    {
        abort_unless($importedFile->user_id === auth()->id(), 403);

        $importedFile->transactions()->delete();
        $importedFile->delete();

        return Redirect::route('profile.edit')->with('status', 'import-deleted');
    }

    /**
     * Eksport aktywnych subskrypcji do pliku CSV.
     */
    public function exportSubscriptions(Request $request)
    {
        $query = Transaction::where('user_id', auth()->id())
            ->where('is_subscription', true);

        if ($request->filled('search')) {
            $query->where('counterparty', 'like', '%' . $request->input('search') . '%');
        }
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->input('date_to'));
        }

        $subscriptions = $query->orderBy('transaction_date', 'desc')->get();

        $rows = [];
        $rows[] = ['Data', 'Kontrahent', 'Tytuł', 'Kwota', 'Waluta'];

        foreach ($subscriptions as $subscription) {
            $rows[] = [
                $subscription->transaction_date,
                $subscription->counterparty,
                $subscription->title,
                number_format($subscription->amount, 2, '.', ''),
                $subscription->currency,
            ];
        }

        $filename = 'subskrypcje-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $handle = fopen('php://temp', 'r+');

        foreach ($rows as $row) {
            fputcsv($handle, $row, ';');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * METODA PREMIUM: Obsługa widoku analityki, filtrów, sortowania (READ)
     */
    public function analytics(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);

        // Podstawa zapytania: tylko wydatki ujemne zalogowanego użytkownika
        $query = Transaction::where('user_id', auth()->id())
                            ->where('amount', '<', 0);

        if ($request->filled('search')) {
            $query->where('counterparty', 'like', '%' . $request->input('search') . '%');
        }
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->input('date_to'));
        }

        $sortComposite = $request->input('sort_composite', 'transaction_date_desc');
        switch ($sortComposite) {
            case 'transaction_date_asc':
                $query->orderBy('transaction_date', 'asc');
                break;
            case 'amount_desc':
                $query->orderBy('amount', 'asc'); 
                break;
            case 'amount_asc':
                $query->orderBy('amount', 'desc');
                break;
            case 'transaction_date_desc':
            default:
                $query->orderBy('transaction_date', 'desc');
                break;
        }

        $transactions = $query->paginate($perPage)->withQueryString();

        // Statystyki do wykresu kołowego (Top 5 struktury wydatków subskrypcji)
        $rawChartStats = Transaction::selectRaw('counterparty, amount')
            ->where('user_id', auth()->id())
            ->where('amount', '<', 0)
            ->get();

        $chartStats = $rawChartStats->groupBy('counterparty')->map(function ($group) {
            return (object) [
                'counterparty' => $group->first()->counterparty,
                'total' => $group->sum(function ($tx) {
                    return abs($tx->amount);
                })
            ];
        })->sortByDesc('total')->take(5);

        if ($chartStats->sum('total') == 0) {
            $chartStats = collect();
        }
        
        $monthlyData = Transaction::where('user_id', auth()->id())
            ->where('amount', '<', 0)
            ->selectRaw("strftime('%Y-%m', transaction_date) as month") 
            ->selectRaw("SUM(ABS(amount)) as total_spent")
            ->selectRaw("SUM(CASE WHEN is_subscription = 0 THEN ABS(amount) ELSE 0 END) as spent_without_subs")
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();

        if ($monthlyData->isEmpty()) {
            $chartData = ['labels' => [], 'total' => [], 'no_subs' => []];
        } else {
            $chartData = [
                'labels' => $monthlyData->pluck('month'),
                'total' => $monthlyData->pluck('total_spent'),
                'no_subs' => $monthlyData->pluck('spent_without_subs')
            ];
        }

        return view('analytics', [
            'transactions' => $transactions,
            'chartStats' => $chartStats,
            'chartData' => $chartData,
            'premiumStats' => (bool) auth()->user()->is_premium
        ]);
    }

    /**
     * [CRUD: CREATE] Ręczne wprowadzanie nowej subskrypcji z poziomu modala
     */
    public function storeSubscription(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'counterparty' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
        ]);

        Transaction::create([
            'user_id' => auth()->id(),
            'counterparty' => $validated['counterparty'],
            'title' => $validated['title'],
            'amount' => -abs($validated['amount']), // Zapisujemy jako wydatek pasywny (wartość ujemna)
            'transaction_date' => $validated['transaction_date'],
            'currency' => 'PLN',
            'is_subscription' => true, // Flaga ustawiana automatycznie na true
        ]);

        return redirect()->back()->with('success', 'Subskrypcja została pomyślnie dodana do systemu!');
    }

    /**
     * [CRUD: UPDATE] Zapisywanie zmodyfikowanych danych istniejącego rekordu subskrypcji
     */
    public function updateSubscription(Request $request, $id): RedirectResponse
    {
        $transaction = Transaction::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'counterparty' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
        ]);

        $transaction->update([
            'counterparty' => $validated['counterparty'],
            'title' => $validated['title'],
            'amount' => -abs($validated['amount']), // Wartość ujemna chroni spójność finansową bazy
            'transaction_date' => $validated['transaction_date'],
        ]);

        return redirect()->back()->with('success', 'Dane subskrypcji zostały zaktualizowane.');
    }

    /**
     * [CRUD: DELETE] Przełączanie statusu subskrypcji (Zaznacz / Odznacz rekord jako subskrypcję)
     */
    public function toggleSubscription($id): RedirectResponse
    {
        $transaction = Transaction::where('user_id', auth()->id())->findOrFail($id);
        
        $transaction->is_subscription = !$transaction->is_subscription;
        $transaction->save();

        return redirect()->back()->with('success', 'Zmieniono status subskrypcji dla kontrahenta: ' . $transaction->counterparty);
    }

    /**
     * METODA PREMIUM: Rezygnacja z subskrypcji - usuwanie przyszłych zaplanowanych wierszy
     */
    public function cancelSubscription(Request $request): RedirectResponse
    {
        $request->validate([
            'counterparty' => 'required|string',
            'start_date' => 'required|date',
            'months' => 'required',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $monthsNumeric = (int) preg_replace('/[^0-9]/', '', $request->months);

        if ($monthsNumeric < 1) {
            $monthsNumeric = 1;
        }

        $endDate = $startDate->copy()->addMonths($monthsNumeric);

        Transaction::where('user_id', auth()->id())
            ->where('counterparty', $request->counterparty)
            ->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->delete();

        return redirect()->back()->with('success', 'Pomyślnie zasymulowano rezygnację z subskrypcji. Określone płatności zostały usunięte z bazy danych.');
    }
}