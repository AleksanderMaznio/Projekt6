<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    // Pozwalamy na automatyczny zapis tych kolumn
    protected $fillable = [
        'user_id', 
        'name', 
        'price', 
        'currency', 
        'billing_cycle_days', 
        'next_billing_date', 
        'is_active'
    ];
}