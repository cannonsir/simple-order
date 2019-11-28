<?php

namespace Gtd\SimpleOrder\Traits;

use Gtd\SimpleOrder\Models\Amount;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasAmount
{
    public function amount(): HasOne
    {
        return $this->hasOne(Amount::class, 'amount_id', 'id');
    }
}