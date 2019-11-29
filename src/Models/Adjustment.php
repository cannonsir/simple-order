<?php

namespace Gtd\SimpleOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Adjustment extends Model
{
    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('simple-order.table_names.adjustments'));
    }

    public function amount(): BelongsTo
    {
        return $this->belongsTo(Amount::class, 'amount_id');
    }

    public function adjustable()
    {
        return $this->morphTo();
    }
}