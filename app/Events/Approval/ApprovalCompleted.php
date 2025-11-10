<?php

namespace App\Events\Approval;

use App\Models\Approval\ApprovalRequest;

class ApprovalCompleted
{
    /**
     * Create a new event instance.
     *
     * @param object $model The model associated with the approval.
     * @param ApprovalRequest $request The approval request instance.
     * @param object $user The user who completed the approval.
     */
    public function __construct(
        public object $model,
        public ApprovalRequest $request,
        public object $user
    ) {}
}
