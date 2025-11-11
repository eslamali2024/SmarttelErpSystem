<?php

namespace Modules\Hr\Enums;

enum LeaveTypeDurationEnum: int
{
    case FIXED                    = 1;
    case STATIC_HOUR_HALF_MORNING = 2;
    case STATIC_HOUR_HALF_NIGHT   = 3;

    public function label(): string
    {
        return match ($this) {
            self::FIXED                    => __('hr.fixed'),
            self::STATIC_HOUR_HALF_MORNING => __('hr.static_hour_half_morning'),
            self::STATIC_HOUR_HALF_NIGHT   => __('hr.static_hour_half_night'),
        };
    }

    public static function items(): array
    {
        return [
            self::FIXED->value                    => self::FIXED->label(),
            self::STATIC_HOUR_HALF_MORNING->value => self::STATIC_HOUR_HALF_MORNING->label(),
            self::STATIC_HOUR_HALF_NIGHT->value   => self::STATIC_HOUR_HALF_NIGHT->label(),
        ];
    }
}
