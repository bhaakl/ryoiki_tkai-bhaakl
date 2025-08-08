<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $customer_id
 * @property $email
 * @property $code
 */
class EmailChange extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'customer_id',
        'email',
        'code'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
