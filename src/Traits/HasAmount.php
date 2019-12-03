<?php

namespace Gtd\SimpleOrder\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasAmount
{
    public function amount(): HasOne
    {
        switch (get_class($this)) {
            case config('simple-order.models.Order') :
                $foreignKey = 'order_id';
                break;
            case config('simple-order.models.OrderItem') :
                $foreignKey = 'order_item_id';
                break;
            case config('simple-order.models.OrderItemUnit') :
                $foreignKey = 'order_item_unit_id';
                break;
            default :
                $foreignKey = null;
        }

        return $this->hasOne(config('simple-order.models.Amount'), $foreignKey);
    }

    public function getAdjustmentsTotal(): string
    {
        return $this->amount->adjustments_amount_total;
    }

    public function setAdjustmentsTotal($adjustments_amount_total)
    {
        $this->amount->update(compact('adjustments_amount_total'));
    }

    public function getOriginAmount(): string
    {
        return $this->amount->should_amount;
    }

    public function setOriginAmount($should_amount)
    {
        $this->amount->update(compact('should_amount'));
    }

    public function getResAmount(): string
    {
        return $this->amount->res_amount;
    }

    public function setResAmount($res_amount)
    {
        $this->amount->update(compact('res_amount'));
    }

    public function calculateAdjustmentsTotal(): string
    {
        return $this->adjustments->sum->amount;
    }

    public function calculateResAmount(): string
    {
        return bcadd($this->getOriginAmount(), $this->getAdjustmentsTotal(), config('simple-order.decimal_precision.places'));
    }
}