<?php

namespace App\Observers;

use App\Models\Property;
use Illuminate\Support\Str;

class PropertyObserver
{
    public function creating(Property $property): void
    {
        if (!$property->title) {
            $property->title = Str::upper(Str::snake(Str::slug($property->name)));
        }
    }

    public function updating(Property $property): void
    {
        if (!$property->title) {
            $property->title = Str::upper(Str::snake(Str::slug($property->name)));
        }
    }
}
