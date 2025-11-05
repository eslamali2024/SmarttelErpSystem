<?php

namespace Modules\Hr\Models;

use App\Traits\ScopeFilter;
use Illuminate\Database\Eloquent\Model;
use Modules\Hr\Enums\AllowancesTypeEnum;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Hr\Enums\AllowancesTaxableEnum;

class AllowanceType extends Model
{
    use SoftDeletes, ScopeFilter;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'type',
        'taxable',
    ];

    protected $appends = [
        'allowance_type',
        'allowance_taxable',
    ];

    protected $casts = [
        'type'      => AllowancesTypeEnum::class,
        'taxable'   => AllowancesTaxableEnum::class
    ];

    /**
     * Return the label of the type attribute.
     *
     * @return string|null
     */
    public function getAllowanceTypeAttribute()
    {
        return $this->type?->label();
    }

    /**
     * Return the label of the taxable attribute.
     *
     * @return string|null
     */
    public function getAllowanceTaxableAttribute()
    {
        return $this->taxable?->label();
    }
}
