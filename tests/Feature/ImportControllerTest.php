<?php

use App\Models\ImportedFile;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\UploadedFile;

it('rejects non-csv uploads', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('import.store'), [
        'csv_file' => UploadedFile::fake()->create('statement.txt', 100, 'text/plain'),
    ]);

    $response->assertSessionHasErrors('csv_file');
    $response->assertStatus(302);
});

it('allows deleting an imported csv batch from the profile page', function () {
    $user = User::factory()->create();
    $importedFile = ImportedFile::create([
        'user_id' => $user->id,
        'file_name' => 'statements.csv',
    ]);
    $transaction = Transaction::create([
        'user_id' => $user->id,
        'transaction_date' => '2026-06-01',
        'amount' => -100,
        'currency' => 'PLN',
        'title' => 'Zakup',
        'counterparty' => 'Sklep',
        'is_subscription' => false,
        'imported_file_id' => $importedFile->id,
    ]);

    $response = $this->actingAs($user)->delete(route('profile.imports.destroy', $importedFile));

    $response->assertRedirect(route('profile.edit'));
    $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    $this->assertDatabaseMissing('imported_files', ['id' => $importedFile->id]);
});
