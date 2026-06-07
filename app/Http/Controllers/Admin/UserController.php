<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // 1. Walidujemy podstawowe dane oraz upewniamy się, że rola to jedna z trzech opcji
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:user,premium,admin', // <-- nowa walidacja roli
        ]);

        // 2. Mapujemy wybraną rolę z formularza na odpowiednie flagi boolean w bazie danych
        if ($request->role === 'admin') {
            $user->is_admin = true;
            $user->is_premium = false;
        } elseif ($request->role === 'premium') {
            $user->is_admin = false;
            $user->is_premium = true;
        } else {
            // Zwykły użytkownik ('user')
            $user->is_admin = false;
            $user->is_premium = false;
        }

        // 3. Aktualizujemy imię i mail z walidacji, a następnie zapisujemy wszystko razem
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Użytkownik zaktualizowany!');
    }

    public function destroy(User $user)
    {
        // 1. Nie pozwól usunąć samego siebie
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Nie możesz usunąć własnego konta!');
        }

        // 2. Nie pozwól usunąć innego administratora
        if ($user->is_admin) {
            return back()->with('error', 'Nie możesz usunąć konta administratora!');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Użytkownik usunięty.');
    }
}