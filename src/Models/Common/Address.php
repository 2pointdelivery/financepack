<?php

namespace FinancePack\Models\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    protected $table = 'addresses';

    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'line_one',
        'line_two',
        'city',
        'state',
        'postal_code',
        'country',
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
