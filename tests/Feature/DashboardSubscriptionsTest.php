<?php

use App\Models\Transaction;
use App\Models\User;

it('groups duplicate subscription entries by counterparty on the dashboard', function () {
    $user = User::factory()->create();

    Transaction::create([
        'user_id' => $user->id,
        'transaction_date' => '2026-01-10',
        'amount' => -19.99,
        'currency' => 'PLN',
        'title' => 'Abonament',
        'counterparty' => 'Netflix',
        'is_subscription' => true,
    ]);

    Transaction::create([
        'user_id' => $user->id,
        'transaction_date' => '2026-02-10',
        'amount' => -19.99,
        'currency' => 'PLN',
        'title' => 'Abonament',
        'counterparty' => 'Netflix',
        'is_subscription' => true,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertViewHas('subscriptions', function ($subscriptions) {
        return $subscriptions->count() === 1;
    });
    $response->assertSee('Netflix');
});
