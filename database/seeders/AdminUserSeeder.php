<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@admin.pl'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'is_premium' => false, // jawnie ustawiamy brak premium dla czystości kodu
            ]
        );

        // Tworzenie/aktualizacja użytkownika Premium
        User::updateOrCreate(
            ['email' => 'premium@premium.pl'], // unikalny mail do logowania
            [
                'name' => 'Użytkownik Premium',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'is_premium' => true, // <-- To konto dostaje premium
            ]
        );
    }
}