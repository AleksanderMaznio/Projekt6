<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Transaction; // Importujemy model transakcji
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Importujemy DB fasadę do czyszczenia tabeli

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tworzenie standardowego użytkownika testowego
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
 
        // 2. Wywołanie pozostałych seederów (np. konta administratora)
        $this->call([
            AdminUserSeeder::class,
        ]);

        // 3. CZYSZCZENIE PRZYCHODÓW Z BAZY DATA
        // Ta instrukcja uruchomi się na samym końcu seedowania. 
        // Przejrzy całą tabelę 'transactions' i usunie każdy rekord, którego kwota jest większa lub równa 0.
        DB::table('transactions')->where('amount', '>=', 0)->delete();
    }
}