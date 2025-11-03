<?php

namespace Modules\Hr\Models;

use App\Traits\ScopeFilter;
use Modules\Hr\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Model;
use Modules\Hr\Enums\MaritalStatusEnum;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
