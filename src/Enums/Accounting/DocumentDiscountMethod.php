<?php

namespace FinancePack\Enums\Accounting;

enum DocumentDiscountMethod: string
{
    case PerLineItem = 'per_line_item';
    case PerDocument = 'per_document';

    public function getLabel(): string
    {
        return match ($this) {
            self::PerLineItem => 'Per Line Item',
            self::PerDocument => 'Per Document',
        };
    }

    public function isPerLineItem(): bool
    {
        return $this === self::PerLineItem;
    }

    public function isPerDocument(): bool
    {
        return $this === self::PerDocument;
    }
}
