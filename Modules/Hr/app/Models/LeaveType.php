<?php

namespace Modules\Hr\Models;

use App\Traits\ScopeFilter;
use Illuminate\Database\Eloquent\Model;
use Modules\Hr\Enums\LeaveTypeUnitEnum;
use Modules\Hr\Enums\LeaveTypeDurationEnum;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use SoftDeletes, ScopeFilter;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'unit',
        'for_employee',
        'company_amount',
        'duration_deducted_percentage',
        'salary_deducted_percentage',
        'type_duration',
    ];

    protected $casts = [
        'type_duration'                     => LeaveTypeDurationEnum::class,
        'unit'                              => LeaveTypeUnitEnum::class,
        'for_employee'                      => 'boolean',
        'duration_deducted_percentage'      => 'integer',
        'salary_deducted_percentage'        => 'integer',
        'company_amount'                    => 'integer',
    ];

    protected $appends = [
        'unit_label',
        'type_duration_label'
    ];

    /**
     * Get the label of the unit of the leave type.
     *
     * @return string
     */
    public function getUnitLabelAttribute(): string
    {
        return $this->unit?->label() ?? '';
    }

    /**
     * Get the label of the type duration of the leave type.
     *
     * @return string
     */
    public function getTypeDurationLabelAttribute(): string
    {
        return $this->type_duration?->label() ?? '';
    }
}
