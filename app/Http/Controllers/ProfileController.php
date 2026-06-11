<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Transaction; // Służy do operacji na tabeli transakcji
use Carbon\Carbon;           // Służy do manipulacji datami

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
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
     * METODA PREMIUM: Obsługa widoku analityki, filtrów, sortowania (W pełni kompatybilna z SQLite)
     */
    public function analytics(Request $request)
    {
        // 1. Definiowanie dynamicznego limitu transakcji na stronę z formularza (domyślnie 15)
        $perPage = (int) $request->input('per_page', 15);

        // Podstawa zapytania: tylko transakcje ZALOGOWANEGO użytkownika i tylko WYDATKI (kwoty ujemne)
        $query = Transaction::where('user_id', auth()->id())
                            ->where('amount', '<', 0);

        // 2. Filtrowanie wyszukiwarki i dat
        if ($request->filled('search')) {
            $query->where('counterparty', 'like', '%' . $request->input('search') . '%');
        }
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->input('date_to'));
        }

        // 3. Obsługa sortowania z formularza
        $sortComposite = $request->input('sort_composite', 'transaction_date_desc');
        switch ($sortComposite) {
            case 'transaction_date_asc':
                $query->orderBy('transaction_date', 'asc');
                break;
            case 'amount_desc':
                $query->orderBy('amount', 'asc'); // Kwoty ujemne, więc im "niższa" liczba tym większy wydatek w bazie
                break;
            case 'amount_asc':
                $query->orderBy('amount', 'desc');
                break;
            case 'transaction_date_desc':
            default:
                $query->orderBy('transaction_date', 'desc');
                break;
        }

        // 4. Pobieranie danych przez elegancką i bezpieczną paginację Laravela
        // z zachowaniem filtrów w adresie URL przy zmianie stron (withQueryString)
        $transactions = $query->paginate($perPage)->withQueryString();

        // 5. Statystyki do wykresu (Top 5 z całości danych bez wpływu podziału na strony tabeli)
        $rawChartStats = Transaction::selectRaw('counterparty, amount')
            ->where('user_id', auth()->id())
            ->where('amount', '<', 0)
            ->get();

        $chartStats = $rawChartStats->groupBy('counterparty')->map(function ($group) {
            return (object) [
                'counterparty' => $group->first()->counterparty,
                'total' => $group->sum(function ($tx) {
                    return abs($tx->amount); // abs() w PHP działa bezbłędnie na SQLite
                })
            ];
        })->sortByDesc('total')->take(5);

        if ($chartStats->sum('total') == 0) {
            $chartStats = collect();
        }

        // Zwrócenie widoku z wbudowaną paginacją przekazaną bezpośrednio w $transactions
        return view('analytics', [
            'transactions' => $transactions,
            'chartStats' => $chartStats,
            'premiumStats' => true
        ]);
    }

    /**
     * Przełączanie statusu subskrypcji (Zaznacz / Odznacz rekord)
     */
    public function toggleSubscription($id): RedirectResponse
    {
        $transaction = Transaction::where('user_id', auth()->id())->findOrFail($id);
        
        $transaction->is_subscription = !$transaction->is_subscription;
        $transaction->save();

        return redirect()->back()->with('success', 'Zmieniono status subskrypcji dla kontrahenta: ' . $transaction->counterparty);
    }

    /**
     * METODA PREMIUM: Rezygnacja z subskrypcji - kasowanie przyszłych wierszy z bazy danych
     */
    public function cancelSubscription(Request $request): RedirectResponse
    {
        $request->validate([
            'counterparty' => 'required|string',
            'start_date' => 'required|date',
            'months' => 'required',
        ]);

        $startDate = Carbon::parse($request->start_date);
        
        // Wyciągamy samą cyfrę (np. "1") z tekstu "1 miesiąc", "3 mieś." itd.
        $monthsNumeric = (int) preg_replace('/[^0-9]/', '', $request->months);

        if ($monthsNumeric < 1) {
            $monthsNumeric = 1;
        }

        $endDate = $startDate->copy()->addMonths($monthsNumeric);

        // Usuwamy transakcje wybranego usługodawcy w zdefiniowanym przedziale czasowym
        Transaction::where('user_id', auth()->id())
            ->where('counterparty', $request->counterparty)
            ->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->delete();

        return redirect()->back()->with('with', 'success', 'Pomyślnie zasymulowano rezygnację z subskrypcji. Określone płatności zostały usunięte z bazy danych.');
    }
}