<?php

namespace Modules\Hr\Models;

use Illuminate\Database\Eloquent\Model;

class ContractAllowance extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'contract_id',
        'allowance_id',
        'amount',
    ];
}
