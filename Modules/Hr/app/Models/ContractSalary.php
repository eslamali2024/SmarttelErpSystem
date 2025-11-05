<?php

namespace Modules\Hr\Models;

use App\Models\User;
use App\Traits\ScopeFilter;
use Illuminate\Database\Eloquent\Model;
use Modules\Hr\Models\InsuranceCompany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractSalary extends Model
{
    use SoftDeletes, ScopeFilter;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'contract_id',
        'basic_salary',
        'gross_salary',
        'net_salary',
        'salary_per_day',
        'salary_per_hour',
        'salary_per_year',
        'total_allowances',
        'social_insurance',
        'insurance_wage',
        'insurance_wage_rounded',
        'company_insurance',
        'martyrs_families_fund',
        'tax_monthly',
        'total_deductions',
        'insurance_number',
        'insurance_company_id',
        'created_by',
    ];

    /**
     * Get the employee contract that the contract salary belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(EmployeeContract::class, 'contract_id');
    }

    /**
     * Get the user that created the contract salary.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the insurance company that the contract salary belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function insuranceCompany(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class, 'insurance_company_id');
    }
}
