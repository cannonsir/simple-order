<?php

namespace Gtd\SimpleOrder\Models;

use Gtd\SimpleOrder\Traits\HasAmount;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasAmount;

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('simple-order.table_names.order_items'));
    }

    public function orderable()
    {
        return $this->morphTo('orderable');
    }

    public function units()
    {
        return $this->hasMany(OrderItemUnit::class, 'item_id');
    }
}