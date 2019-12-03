<?php

namespace Gtd\SimpleOrder\Models;

use Gtd\SimpleOrder\Exceptions\InvalidAmountException;
use Gtd\SimpleOrder\Traits\HasAdjustments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Amount extends Model
{
    use HasAdjustments;

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('simple-order.table_names.amounts'));
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (self $self) {
            if (isset($self->should_amount) && !is_numeric($self->should_amount) || $self->should_amount < 0) {
                throw new InvalidAmountException('金额非法');
            }

            // 确保金额大于0
            if (isset($self->res_amount) && !is_numeric($self->res_amount) || $self->res_amount < 0) {
                throw new InvalidAmountException('金额非法');
            }
        });
    }

    public function belong(): BelongsTo
    {
        if ($this->order_id) {
            $related = config('simple-order.models.Order');
            $foreignKey = 'order_id';
        } elseif ($this->order_item_id) {
            $related = config('simple-order.models.OrderItem');
            $foreignKey = 'order_item_id';
        } elseif ($this->order_item_unit_id) {
            $related = config('simple-order.models.OrderUnit');
            $foreignKey = 'order_item_unit_id';
        } else {
            $related = null;
            $foreignKey = null;
        }

        return $this->belongsTo($related, $foreignKey);
    }
}