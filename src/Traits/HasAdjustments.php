<?php

namespace Gtd\SimpleOrder\Traits;

use Gtd\SimpleOrder\Models\Adjustment;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

trait HasAdjustments
{
    public function adjustments(): HasMany
    {
        return $this->hasMany(Adjustment::class);
    }

    public function addAdjustment(array $attributes = [])
    {
        return $this->adjustments()->save(new Adjustment($attributes));
    }
}