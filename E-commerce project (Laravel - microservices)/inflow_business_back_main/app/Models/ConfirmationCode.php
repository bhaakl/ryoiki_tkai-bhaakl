<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ConfirmationCode extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'customer_id',
        'type',
        'value',
        'code',
        'created_at'
    ];
}
