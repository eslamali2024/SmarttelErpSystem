<?php

namespace App\Contracts\Approval;

use App\Models\User;
use App\Models\Approval\ApprovalRequest;

interface ApprovalModel
{
    /**
     * Handles actions to be performed when an approval request is created.
     *
     * @param ApprovalRequest $request The approval request instance.
     * @param User $user The user who created the approval request.
     *
     */
    public function onCreateRequestApproval(ApprovalRequest $request, User $user): void;

    /**
     * Handles actions to be performed when an approval is first started.
     *
     * @param ApprovalRequest $request The approval request instance.
     * @param User $user The user who started the approval.
     * */
    public function onCompletedFirstApprove(ApprovalRequest $request, User $user): void;

    /**
     * Handles actions to be performed when an approval is completed.
     *
     * @param ApprovalRequest $request The approval request instance.
     * @param User $user The user who completed the approval.
     */
    public function onCompletedApprove(ApprovalRequest $request, User $user): void;

    /**
     * Handles actions to be performed when an approval is canceled.
     *
     * @param ApprovalRequest $request The approval request instance.
     * @param User $user The user who canceled the approval.
     */
    public function onCancelCompletedApprove(ApprovalRequest $request, User $user): void;

    /**
     * Handles actions to be performed when an approval is canceled.
     *
     * @param ApprovalRequest $request The approval request instance.
     * @param User $user The user who canceled the approval.
     *
     */
    public function onCancelApprove(ApprovalRequest $request, User $user): void;

    /**
     * Handles actions to be performed when an approval is rejected.
     *
     * @param ApprovalRequest $request The approval request instance.
     * @param User $user The user who rejected the approval.
     */
    public function onHappenReject(ApprovalRequest $request, User $user): void;
}
