<?php

namespace App\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $ext_id
 * @property $name
 * @property $is_active
 */
class Media extends SpatieMedia
{
    use UsesTenantConnection;

}
