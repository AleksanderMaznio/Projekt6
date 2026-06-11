<?php

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
