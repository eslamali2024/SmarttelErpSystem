<?php

namespace Modules\Hr\Models;

use App\Traits\ScopeFilter;
use Modules\Hr\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Model;
use Modules\Hr\Enums\MaritalStatusEnum;
use Modules\Hr\Enums\ContractStatusEnum;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use ScopeFilter, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'name',
        'name_ar',
        'gender',
        'marital_status',
        'email',
        'phone',
        'address',
        'dob',
        'joining_date',
        'notes',
        'national_id',
    ];

    protected $casts = [
        'gender'            => GenderEnum::class,
        'marital_status'    => MaritalStatusEnum::class,
        'joining_date'      => 'date',
        'dob'               => 'date',
    ];

    public static function autoGenerateCode()
    {
        return 'EMP-' . str_pad(Employee::count() + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the employee contracts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(EmployeeContract::class, 'employee_id');
    }

    /**
     * Get the current active contract of the employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currentContract(): HasOne
    {
        return $this->hasOne(EmployeeContract::class, 'employee_id')->where('status', ContractStatusEnum::ACTIVE);
    }
}
