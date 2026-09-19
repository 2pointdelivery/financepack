<?php

declare(strict_types=1);

namespace FinancePack\Casts;

use Illuminate\Contracts\Casts\CastAttributes;

class RateCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes): ?float
    {
        if ($value === null) {
            return null;
        }

        return (float) $value;
    }

    public function set($model, $key, $value, $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string) (float) $value;
    }
}
