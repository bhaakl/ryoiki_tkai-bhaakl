<?php

namespace App\Models;

use App\Enums\MainPageTemplates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $template
 * @property $title
 * @property $is_active
 * @property $sort
 */
class MainPageBlock extends Model
{
    use UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'template',
        'title',
        'is_active',
        'sort'
    ];

    protected $casts = [
        'template' => MainPageTemplates::class,
    ];

    public function products(): HasMany
    {
        return $this->hasMany(MainPageProduct::class);
    }

    public function promos(): HasMany
    {
        return $this->hasMany(Promo::class);
    }

    public function getProducts(Request $request, $version = 'v1')
    {
        $products = $this->products()->pluck('product_id')->toArray();
        $products = implode(',', $products);
        $query = http_build_query([
            'ids' => $products,
            'page' => $request->page ?? 1,
            'per_page' => $request->per_page ?? 15,
        ]);
        $response = Http::withHeaders([
            'tenant-uuid' => app('currentTenant')->uuid
        ])->get(config('app.catalog_url') . '/api/' . $version . '/products/ids?' . $query);

        return $response->ok() ? $response->object() : [];
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', 1);
    }
}
