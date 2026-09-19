<?php

namespace FinancePack\Models\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Contact extends Model
{
    protected $table = 'contacts';

    protected $fillable = [
        'contactable_type',
        'contactable_id',
        'name',
        'email',
        'phone',
        'position',
    ];

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }
}
