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
     * METODA PREMIUM: Obsługa widoku analityki, filtrów, sortowania (Kompatybilna z SQLite)
     */
    public function analytics(Request $request): View
    {
        // 1. NAPRAWA BŁĘDU: Pobieramy zalogowanego użytkownika na samym początku metody
        $user = auth()->user();
        
        // 2. WYMUSZENIE PREMIUM: Do testów ustawione na true lub dynamicznie z modelu
        $isPremium = true; 

        // Pobieramy tylko i wyłącznie wydatki (kwoty ujemne) zalogowanego użytkownika
        $query = Transaction::where('user_id', $user->id)->where('amount', '<', 0);

        if ($isPremium) {
            // 1. Filtrowanie po nazwie kontrahenta lub tytule
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('counterparty', 'like', '%' . $request->search . '%')
                      ->orWhere('title', 'like', '%' . $request->search . '%');
                });
            }

            // 2. Filtrowanie po zakresie dat
            if ($request->filled('date_from')) {
                $query->where('transaction_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->where('transaction_date', '<=', $request->date_to);
            }

            // 3. Dynamiczne sortowanie kompatybilne z widokiem (obsługa osobnych pól sort_by i order)
            $sortField = $request->get('sort_by', 'transaction_date');
            $sortOrder = $request->get('order', 'desc');
            
            if (in_array($sortField, ['transaction_date', 'amount', 'counterparty'])) {
                if ($sortField === 'amount') {
                    // Ponieważ wydatki są ujemne, odwracamy kierunek dla intuicyjnego sortowania
                    $query->orderBy('amount', $sortOrder === 'desc' ? 'asc' : 'desc');
                } else {
                    $query->orderBy($sortField, $sortOrder);
                }
            }
        } else {
            // Domyślne sortowanie dla użytkowników bez Premium
            $query->orderBy('transaction_date', 'desc');
        }

        // Wyniki do głównej tabeli operacji
        $transactions = $query->get();

        // Wykres Top 5 największych wydatków
        $chartStats = Transaction::where('user_id', $user->id)
            ->where('amount', '<', 0)
            ->selectRaw('counterparty, ABS(SUM(amount)) as total')
            ->groupBy('counterparty')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Statystyki Trendów miesięcznych - dostosowane pod SQLite
        $premiumStats = $isPremium ? [
            'average_expense' => abs(Transaction::where('user_id', $user->id)->where('amount', '<', 0)->avg('amount') ?? 0),
            'monthly_expenses' => Transaction::where('user_id', $user->id)
                ->where('amount', '<', 0)
                ->selectRaw("strftime('%Y-%m', transaction_date) as month, ABS(SUM(amount)) as total")
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get()
        ] : null;

        return view('analytics', compact('chartStats', 'premiumStats', 'transactions'));
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
            'months' => 'required|string', // Zmienione na string, by przyjąć wartość "1 miesiąc"
        ]);

        $startDate = Carbon::parse($request->start_date);
        
        // NAPRAWA TypeError: Wyciągamy samą cyfrę (np. "1") z tekstu "1 miesiąc"
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

        return redirect()->back()->with('success', 'Pomyślnie zasymulowano rezygnację z subskrypcji. Określone płatności zostały usunięte z bazy danych.');
    }
}