<?php

namespace Modules\Hr\Models;

use App\Traits\ScopeFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeManagement extends Model
{
    use SoftDeletes, ScopeFilter;

    protected $table = 'time_managements';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'payroll',
        'fingerprint_in',
        'fingerprint_out',
        'factors',
    ];
}
