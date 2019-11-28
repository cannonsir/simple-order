<?php

namespace Gtd\SimpleOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Amount extends Model
{
    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('simple-order.table_names.amounts'));
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(Adjustment::class);
    }
}