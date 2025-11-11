<?php

namespace App\Models\Approval;

use App\Models\User;
use App\Enums\Approval\ApprovalStatusEnum;
use Illuminate\Database\Eloquent\Model;

class ApprovalRequestAction extends Model
{
    protected $fillable = [
        'approval_flow_id',
        'approval_flow_step_id',
        'action_type',
        'approver_name',
        'approver_id',
        'comment',
        'approval_request_id'
    ];

    protected $casts = [
        'action_type' => ApprovalStatusEnum::class,
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function approvalFlow()
    {
        return $this->belongsTo(ApprovalFlow::class);
    }

    public function approvalFlowStep()
    {
        return $this->belongsTo(ApprovalFlowStep::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
