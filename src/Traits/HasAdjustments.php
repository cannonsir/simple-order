<?php

namespace Gtd\SimpleOrder\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasAdjustments
{
    public function adjustments(): HasMany
    {
        return $this->hasMany(config('simple-order.models.Adjustment'));
    }

    public function addAdjustment(string $label, $amount = 0)
    {
        $attributes = is_array($label) ? $label : compact('label', 'amount');

        $adjustmentClass = config('simple-order.models.Adjustment');

        return $this->adjustments()->save(new $adjustmentClass($attributes));
    }

    public function addUnIncludedAdjustment(string $label, $amount = 0)
    {
        $attributes = is_array($label) ? $label : compact('label', 'amount');

        $attributes['included'] = false;

        $adjustmentClass = config('simple-order.models.Adjustment');

        return $this->adjustments()->save(new $adjustmentClass($attributes));
    }
}