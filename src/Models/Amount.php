<?php

namespace Gtd\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Amount extends Model
{
    protected $guarded = ['id'];

    public function adjustments(): HasMany
    {
        return $this->hasMany(Adjustment::class);
    }
}