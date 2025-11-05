<?php

namespace Modules\Hr\Models;

use App\Traits\ScopeFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsuranceCompany extends Model
{
    use SoftDeletes, ScopeFilter;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'website',
    ];
}
