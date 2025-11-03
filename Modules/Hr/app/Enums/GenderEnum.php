<?php

namespace Modules\Hr\Enums;

enum GenderEnum: int
{
    case MALE   = 1;
    case FEMALE = 2;

    public function label(): string
    {
        return match ($this) {
            self::MALE   => __('hr.male'),
            self::FEMALE => __('hr.female'),
        };
    }

    public static function items(): array
    {
        return [
            self::MALE->value   => self::MALE->label(),
            self::FEMALE->value => self::FEMALE->label(),
        ];
    }
}
