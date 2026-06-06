<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'transaction_date', 'amount', 'currency', 'title', 'counterparty', 'is_subscription'
    ];
}