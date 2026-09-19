<?php

namespace FinancePack\Enums\Concerns;

trait ParsesEnum
{
    public static function parse($value): ?static
    {
        if ($value instanceof static) {
            return $value;
        }

        if (is_string($value)) {
            return static::tryFrom($value) ?? null;
        }

        return null;
    }

    public static function parseFromLabel(string $label): ?static
    {
        foreach (static::cases() as $case) {
            if (strtolower($case->getLabel()) === strtolower($label)) {
                return $case;
            }
        }

        return null;
    }
}
