<?php

namespace Modules\Hr\Models;

use App\Models\User;
use App\Traits\ScopeFilter;
use Modules\Hr\Enums\GenderEnum;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Modules\Hr\Enums\MaritalStatusEnum;
use Modules\Hr\Enums\ContractStatusEnum;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model implements HasMedia
{
    use ScopeFilter, SoftDeletes, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
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
        'status',
    ];

    protected $casts = [
        'gender'            => GenderEnum::class,
        'marital_status'    => MaritalStatusEnum::class,
        'status'            => ContractStatusEnum::class,
        'joining_date'      => 'date',
        'dob'               => 'date',
    ];

    protected $appends = ['status_label', 'gender_label', 'marital_status_label'];

    public static function autoGenerateCode()
    {
        return str_pad(Employee::count() + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the user associated with the employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
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

    /**
     * Get the current active contract position of the employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currentPosition(): HasOne
    {
        return $this->hasOne(ContractPosition::class, 'contract_id')->where('status', ContractStatusEnum::ACTIVE);
    }

    /**
     * Get the label of the status of the employee.
     *
     * @return string|null
     */
    public function getStatusLabelAttribute()
    {
        return $this->status?->label();
    }

    /**
     * Get the label of the gender of the employee.
     *
     * @return string|null
     */
    public function getGenderLabelAttribute()
    {
        return $this->gender?->label();
    }

    /**
     * Get the label of the marital status of the employee.
     *
     * @return string|null
     */
    public function getMaritalStatusLabelAttribute()
    {
        return $this->marital_status?->label();
    }

    /**
     * Get the URL of the avatar image of the employee.
     *
     * @return string|null
     */
    public function getAvatarAttribute()
    {
        return $this->getFirstMediaUrl('avatar');
    }

    /**
     * Get all bonuses of the employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Bonus>
     */
    public function bonuses(): HasMany
    {
        return $this->hasMany(Bonus::class, 'employee_id');
    }

    /**
     * Get all deductions of the employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Deduction>
     */
    public function deductions(): HasMany
    {
        return $this->hasMany(Deduction::class, 'employee_id');
    }
}
