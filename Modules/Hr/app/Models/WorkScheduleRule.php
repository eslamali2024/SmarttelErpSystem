<?php

namespace Modules\Hr\Models;

use App\Traits\ScopeFilter;
use Modules\Hr\Models\WorkSchedule;
use Illuminate\Database\Eloquent\Model;

class WorkScheduleRule extends Model
{
    use ScopeFilter;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'work_schedule_id',
        'deducation_after_time',
        'deducation_percentage',
    ];

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }
}
