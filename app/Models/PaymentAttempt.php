<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'pricing_plan_id',
        'billing_type',
        'expected_amount',
        'currency',
        'three_d_session_id',
        'provider_transaction_id',
        'status',
    ];
}
