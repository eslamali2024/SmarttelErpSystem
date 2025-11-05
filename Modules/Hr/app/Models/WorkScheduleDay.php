<?php

namespace Modules\Hr\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Hr\Enums\DaysInWeekEnum;
use Modules\Hr\Enums\WorkScheduleHolidayStatusEnum;

class WorkScheduleDay extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'work_schedule_id',
        'day',
        'status',
    ];

    protected $casts = [
        'day'    => DaysInWeekEnum::class,
        'status' => WorkScheduleHolidayStatusEnum::class
    ];

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }
}
