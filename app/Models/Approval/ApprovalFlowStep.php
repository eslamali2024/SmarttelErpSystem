<?php

namespace App\Models\Approval;

use App\Models\User;
use App\Traits\ScopeFilter;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Approval\ApprovalTypeEnum;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalFlowStep extends Model
{
    use SoftDeletes, ScopeFilter;

    protected $fillable = [
        'name',
        'approval_flow_id',
        'roles',
        'permissions',
        'approver_type',
        'manager_column',
        'approver_column',
        'order',
        'user_id',
    ];

    protected $casts = [
        'roles'         => 'array',
        'permissions'   => 'array',
        'approver_type' => ApprovalTypeEnum::class,
        'created_at' => 'datetime:Y-m-d H:i:s',

    ];

    protected $appends = [
        'approver_type_label',
    ];

    public function approvalFlow()
    {
        return $this->belongsTo(ApprovalFlow::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getApproverTypeLabelAttribute(): string
    {
        return $this->approver_type?->label() ?? '';
    }
}
