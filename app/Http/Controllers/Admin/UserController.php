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
        // 1. Walidacja danych z formularza
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:user,premium,admin',
        ]);

        // 2. Logika nadawania uprawnień (Admin dostaje true na obie flagi)
        if ($request->role === 'admin') {
            $user->is_admin = true;
            $user->is_premium = true;  // Admin automatycznie ma też premium
        } elseif ($request->role === 'premium') {
            $user->is_admin = false;
            $user->is_premium = true;
        } else {
            $user->is_admin = false;
            $user->is_premium = false;
        }

        // 3. Wypełnienie pozostałych danych i zapis do bazy
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);
        
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Użytkownik zaktualizowany!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Nie możesz usunąć własnego konta!');
        }

        if ($user->is_admin) {
            return back()->with('error', 'Nie możesz usunąć konta administratora!');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Użytkownik usunięty.');
    }
}