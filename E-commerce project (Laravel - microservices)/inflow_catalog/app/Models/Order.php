<?php

namespace App\Models;

use App\Contracts\PaymentSystemContract;
use App\Enums\OrderStatuses;
use App\Filters\QueryFilter;
use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

/**
 * @property $id
 * @property $ext_id
 * @property $user_id
 * @property $user_name
 * @property $user_phone
 * @property $user_email
 * @property $status_id
 * @property $status_code
 * @property $prev_status_id
 * @property $prev_status_code
 * @property $paid_with_bonus
 * @property $total
 * @property $total_with_delivery
 * @property $paid
 * @property $payment_system_id
 * @property $quick
 * @property $delivery_info
 * @property $comment
 * @property $courier_name
 * @property $courier_phone
 * @property $city_id
 * @property $created_at
 * @property $updated_at
 */
#[ObservedBy([OrderObserver::class])]
class Order extends Model
{
    protected $fillable = [
        'ext_id',
        'user_id',
        'user_name',
        'user_email',
        'user_phone',
        'status_id',
        'status_code',
        'prev_status_id',
        'prev_status_code',
        'paid_with_bonus',
        'total',
        'total_with_delivey',
        'paid',
        'payment_system_id',
        'quick',
        'delivery_info',
        'comment',
        'courier_name',
        'courier_phone',
        'city_id',
        'created_at',
        'updated_at',
    ];

    use HasFactory, UsesTenantConnection;

    protected static function boot()
    {
        parent::boot();

        self::creating(function (Order $model) {
            if (!$model->status_code || !$model->status_id) {
                $model->status_code = OrderStatuses::CREATED;
                $model->status_id = OrderStatus::getInitialStatus()->id;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'delivery_info' => 'object',
            'paid' => 'bool',
            'quick' => 'bool',
        ];
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getInitialTotal()
    {
        $total = 0;
        $items = $this->items;
        /** @var OrderItem $item */
        foreach ($items as $item) {
            Log::debug('order item', ['json' => $item->toJson()]);
            $total += $item->source->price * $item->quantity;
        }

        return $total;
    }

    public function getTotal()
    {
        $total = 0;
        $items = $this->items;
        foreach ($items as $item) {
            /** @var OrderItem $item */
            $total += $item->price * $item->quantity;
        }

        return $total;
    }


    public function getDeliveryCost()
    {
        if (isset($this->delivery_info->cost)) {
            return $this->delivery_info->cost;
        }

        return 0;
    }

    public function getTotalWithDelivery()
    {
        return $this->getTotal() + $this->getDeliveryCost();
    }

    public function getDiscountPercent()
    {
        $initialTotal = $this->getInitialTotal();
        if ($initialTotal == 0) {
            return 0;
        }
        $total = $this->getTotal();
        $discount = floor(100 - ($total / $initialTotal * 100));

        return $discount;
    }

    public function getPaymentSystem()
    {
        $service = app(PaymentSystemContract::class);

        return $service->getPaymentSystem($this->getAttribute('payment_system_id'));
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status_code', [
            OrderStatuses::CREATED->value,
            OrderStatuses::PROCESSING->value,
            OrderStatuses::DELIVERING->value,
        ]);
    }

    public function scopeInactive($query)
    {
        return $query->whereIn('status_code', [
            OrderStatuses::DONE->value,
            OrderStatuses::CANCELED->value,
        ]);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter): Builder
    {
        return $filter->apply($builder);
    }
}
