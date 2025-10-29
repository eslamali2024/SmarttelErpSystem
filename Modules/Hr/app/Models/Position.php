<?php

namespace Modules\Hr\Models;

use App\Traits\ScopeFilter;
use Modules\Hr\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Position extends Model
{
    use ScopeFilter, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'employees_count',
        'department_id',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($position) {
            $position->code = $position->code ?? str($position->name)->slug()->prepend('om-pos-');
        });
    }

    /**
     * Get the department that owns the position.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
