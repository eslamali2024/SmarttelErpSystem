<?php

namespace Modules\Hr\Models;

use App\Models\User;
use App\Traits\ScopeFilter;
use App\Traits\Approval\Approvable;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\Approval\ApprovalModel;
use App\Enums\Approval\ApprovalStatusEnum;
use App\Models\Scopes\EmployeeAccessScope;
use App\Traits\Approval\ApprovalModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bonus extends Model implements ApprovalModel
{
    use SoftDeletes, ScopeFilter, Approvable, ApprovalModelTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
        'bonus_type_id',
        'amount',
        'reason',
        'date',
        'notes',
        'status',
        'created_by'
    ];

    protected $casts = [
        'date'        => 'date:Y-m-d',
        'created_at'  => 'date:Y-m-d',
        'status'      => ApprovalStatusEnum::class,
        'amount'      => 'float'
    ];

    protected $appends = ['status_label'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmployeeAccessScope());

        static::creating(function (self $model) {
            $model->created_by = auth()?->user()?->id ?? null;
        });
    }

    /**
     * Get the employee that the bonus belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the bonus type that the bonus belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bonusType(): BelongsTo
    {
        return $this->belongsTo(BonusType::class);
    }

    /**
     * Get the user that created the bonus.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the label of the status of the bonus.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status?->label();
    }
}
