<?php

namespace App\Models\Approval;

use App\Models\User;
use App\Enums\Approval\ApprovalStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalRequest extends Model
{
    protected $fillable = [
        'approval_flow_id',
        'approvable_type',
        'approvable_id',
        'status',
        'creator_id',
    ];

    protected $casts = [
        'status' =>  ApprovalStatusEnum::class,
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function approvable()
    {
        return $this->morphTo();
    }

    public function approvalFlow()
    {
        return $this->belongsTo(ApprovalFlow::class, 'approval_flow_id', 'id');
    }

    public function approvalRequestActions()
    {
        return $this->hasMany(ApprovalRequestAction::class, 'approval_request_id', 'id');
    }

    public function approvalFlowSteps(): HasMany
    {
        return $this->hasMany(ApprovalFlowStep::class, 'approval_flow_id', 'approval_flow_id');
    }

    public function getFirstStep()
    {
        return $this->approvalFlowSteps()->first();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
