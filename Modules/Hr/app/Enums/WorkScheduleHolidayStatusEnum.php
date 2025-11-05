<?php

namespace Modules\Hr\Enums;

enum WorkScheduleHolidayStatusEnum: int
{
    case WORKING     = 1;
    case HOLIDAY     = 2;

    public function label(): string
    {
        return match ($this) {
            static::WORKING     => __('hr.working'),
            static::HOLIDAY     => __('hr.holiday'),
        };
    }
}
