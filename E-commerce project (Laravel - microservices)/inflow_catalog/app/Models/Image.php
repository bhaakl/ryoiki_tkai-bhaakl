<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'imageable_type',
        'imageable_id',
        'original_url',
        'preview_url'
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
