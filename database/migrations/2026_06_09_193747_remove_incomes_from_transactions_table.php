<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Usuwa z bazy danych wszystkie wpisy, gdzie kwota jest dodatnia lub równa zero (przychody)
        DB::table('transactions')->where('amount', '>=', 0)->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Operacja usuwania danych jest nieodwracalna w bezpieczny sposób
    }
};