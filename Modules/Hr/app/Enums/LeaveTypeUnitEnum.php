<?php

namespace Modules\Hr\Enums;

enum LeaveTypeUnitEnum: int
{
    case DAYS                  = 1;
    case HOURS                 = 2;

    public const UNITS = [
        self::DAYS,
        self::HOURS,
    ];

    public function label(): string
    {
        return match ($this) {
            self::DAYS                          => __('hr.days'),
            self::HOURS                         => __('hr.hours'),
        };
    }

    public function value(): string
    {
        return match ($this) {
            self::DAYS                          => 1,
            self::HOURS                         => 2,
        };
    }

    public static function items(): array
    {
        return [
            self::DAYS->value   => self::DAYS->label(),
            self::HOURS->value  => self::HOURS->label(),
        ];
    }
}
