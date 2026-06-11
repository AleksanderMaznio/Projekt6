<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'transaction_date', 'amount', 'currency', 'title', 'counterparty', 'is_subscription', 'imported_file_id'
    ];

    public function importedFile(): BelongsTo
    {
        return $this->belongsTo(ImportedFile::class);
    }
}