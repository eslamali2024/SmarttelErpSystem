<?php

namespace Modules\Hr\Models;

use App\Traits\ScopeFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Hr\Database\Factories\PositionFactory;

class Position extends Model
{
    use ScopeFilter;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
}
