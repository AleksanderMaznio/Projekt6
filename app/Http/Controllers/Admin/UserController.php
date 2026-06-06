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
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,'.$user->id,
        'is_admin' => 'boolean',
    ]);

    $user->update($validated);
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