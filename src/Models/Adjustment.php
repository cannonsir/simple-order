<?php


namespace Gtd\Order\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Adjustment
{
    public function amount(): BelongsTo
    {
        return $this->belongsTo(Amount::class);
    }
}