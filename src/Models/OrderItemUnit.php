<?php

namespace Gtd\SimpleOrder\Models;

use Gtd\SimpleOrder\Traits\HasAdjustments;
use Gtd\SimpleOrder\Traits\HasAmount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemUnit extends Model
{
    use HasAmount, HasAdjustments;

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('simple-order.table_names.order_item_units'));
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function (self $unit) {
            $unitPrice = $unit->orderItem()->first()->getUnitPrice();

            $unit->amount()->create([
                'should_amount' => $unitPrice,
                'res_amount' => $unitPrice,
            ]);
        });
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(config('simple-order.models.OrderItem'), 'item_id');
    }
}