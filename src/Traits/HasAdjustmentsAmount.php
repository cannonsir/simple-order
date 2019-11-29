<?php

namespace Gtd\SimpleOrder\Traits;

use Gtd\SimpleOrder\Models\Adjustment;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasAdjustmentsAmount
{
    public function adjustments(): HasMany
    {
        return $this->hasMany(Adjustment::class);
    }
}