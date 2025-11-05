<?php

namespace Modules\Hr\Enums;

enum AllowancesTypeEnum: int
{
    case RECURRING    = 1;
    case OFF_CYCLE   = 2;

    public function label(): string
    {
        return match ($this) {
            self::RECURRING     => __('hr.recurring'),
            self::OFF_CYCLE     => __('hr.off_cycle'),
        };
    }

    public static function items(): array
    {
        return [
            self::RECURRING->value      => self::RECURRING->label(),
            self::OFF_CYCLE->value      => self::OFF_CYCLE->label(),
        ];
    }

    public static function values(): array
    {
        return [
            self::RECURRING->value,
            self::OFF_CYCLE->value
        ];
    }
}
