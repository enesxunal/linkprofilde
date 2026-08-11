<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'active',
        'name',
        'key',
        'secret',
        'client_id',
        'api_user',
        'api_pass',
    ];

    protected $hidden = [
        'secret',
        'api_pass',
    ];
}
