<?php

namespace Modules\Hr\Models;

use App\Traits\ScopeFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Hr\Enums\ContractStatusEnum;

class EmployeeContract extends Model
{
    use ScopeFilter, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'notes',
        'status',
    ];

    protected $casts = [
        'status'     => ContractStatusEnum::class,
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * Get the employee that the contract belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the contract positions that belong to the employee contract.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contractPositions(): HasMany
    {
        return $this->hasMany(ContractPosition::class, 'contract_id');
    }

    /**
     * Get the current contract position for the employee contract.
     *
     * The current contract position is the contract position that has the status of "ACTIVE".
     *
     * @return \Modules\Hr\Models\ContractPosition|null
     */
    public function currentPosition(): HasOne
    {
        return $this->hasOne(ContractPosition::class, 'contract_id')->where('status', ContractStatusEnum::ACTIVE);
    }

    /**
     * Get the contract salary that belongs to the employee contract.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function salary(): HasOne
    {
        return $this->hasOne(ContractSalary::class, 'contract_id');
    }

    /**
     * Get the contract allowances that belong to the employee contract.
     *
     * The contract allowances are the allowances that are assigned to the employee contract.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allowances(): HasMany
    {
        return $this->hasMany(ContractAllowance::class, 'contract_id');
    }
}
