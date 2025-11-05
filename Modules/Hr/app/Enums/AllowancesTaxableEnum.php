<?php

namespace Modules\Hr\Enums;

enum AllowancesTaxableEnum: int
{
    case NOT_TAXABLE    = 0;
    case TAXABLE        = 1;

    public function label(): string
    {
        return match ($this) {
            self::TAXABLE       => __('hr.taxable'),
            self::NOT_TAXABLE   => __('hr.not_taxable'),
        };
    }

    public static function items(): array
    {
        return [
            self::TAXABLE->value       => self::TAXABLE->label(),
            self::NOT_TAXABLE->value   => self::NOT_TAXABLE->label(),
        ];
    }

    public static function values(): array
    {
        return [
            self::TAXABLE->value,
            self::NOT_TAXABLE->value
        ];
    }
}
