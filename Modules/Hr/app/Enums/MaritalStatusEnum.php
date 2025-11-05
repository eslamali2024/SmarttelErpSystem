<?php

namespace Modules\Hr\Enums;

enum MaritalStatusEnum: int
{
    case MARIED = 1;
    case SINGLE = 2;

    public function label(): string
    {
        return match ($this) {
            self::MARIED => __('hr.maried'),
            self::SINGLE => __('hr.single'),
        };
    }

    public static function items(): array
    {
        return [
            self::MARIED->value => self::MARIED->label(),
            self::SINGLE->value => self::SINGLE->label(),
        ];
    }

    public static function values(): array
    {
        return [
            self::MARIED->value,
            self::SINGLE->value
        ];
    }
}
