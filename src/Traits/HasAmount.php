<?php

namespace Gtd\Order\Traits;

use Gtd\Order\Models\Amount;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasAmount
{
    public function amount(): HasOne
    {
        return $this->hasOne(Amount::class);
    }
}