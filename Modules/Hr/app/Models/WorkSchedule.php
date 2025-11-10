<?php

namespace Modules\Hr\Models;

use App\Traits\ScopeFilter;
use Illuminate\Database\Eloquent\Model;
use Modules\Hr\Models\EmployeeContract;
use Modules\Hr\Models\WorkScheduleRule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkSchedule extends Model
{
    use SoftDeletes, ScopeFilter;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'allow_late_sign_in',
        'allow_early_sign_out',
    ];

    public function employeeContract(): HasMany
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function days(): HasMany
    {
        return $this->hasMany(WorkScheduleDay::class, 'work_schedule_id');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(WorkScheduleRule::class, 'work_schedule_id');
    }
}
