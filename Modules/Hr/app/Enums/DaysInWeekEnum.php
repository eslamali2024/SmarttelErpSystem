<?php

namespace Modules\Hr\Enums;

enum DaysInWeekEnum: int
{
    case SUNDAY     = 1;
    case MONDAY     = 2;
    case TUESDAY    = 3;
    case WEDNESDAY  = 4;
    case THURSDAY   = 5;
    case FRIDAY     = 6;
    case SATURDAY   = 7;
    case ROTATIONHOLIDAY = 8;

    public function label(): string
    {
        return match ($this) {
            static::SUNDAY          => __('hr.sunday'),
            static::MONDAY          => __('hr.monday'),
            static::TUESDAY         => __('hr.tuesday'),
            static::WEDNESDAY       => __('hr.wednesday'),
            static::THURSDAY        => __('hr.thursday'),
            static::FRIDAY          => __('hr.friday'),
            static::SATURDAY        => __('hr.saturday'),
            static::ROTATIONHOLIDAY => __('hr.rotation_holiday'),
        };
    }

    public static function getDays(): array
    {
        return [
            static::SUNDAY->value       => static::SUNDAY,
            static::MONDAY->value       => static::MONDAY,
            static::TUESDAY->value      => static::TUESDAY,
            static::WEDNESDAY->value    => static::WEDNESDAY,
            static::THURSDAY->value     => static::THURSDAY,
            static::FRIDAY->value       => static::FRIDAY,
            static::SATURDAY->value     => static::SATURDAY,
        ];
    }
}
