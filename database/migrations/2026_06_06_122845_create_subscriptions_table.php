<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('subscriptions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('name'); // np. Netflix, Karnet na siłownię
        $table->decimal('price', 8, 2); // Dokładna kwota obciążenia
        $table->string('currency')->default('PLN'); // Waluta
        $table->integer('billing_cycle_days')->default(30); // Częstotliwość płatności w dniach
        $table->date('next_billing_date')->nullable(); // Data następnego ściągnięcia kasy
        $table->boolean('is_active')->default(true); // Status subskrypcji
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
