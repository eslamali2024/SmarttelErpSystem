<?php

namespace App\Http\Controllers\Approval;

use App\Models\Approval\ApprovalRequest;
use App\Enums\Approval\ApprovalStatusEnum;
use App\Http\Controllers\TransactionController;
use App\Http\Requests\Approval\ApprovalFormRequest;

class ApprovalController extends TransactionController
{
    /**
     * Approve the Request.
     *
     * @param ApprovalFormRequest  $request
     * @param  ApprovalRequest $approval_request
     */
    public function approve(ApprovalFormRequest $request, ApprovalRequest $approval_request)
    {
        return $this->withTransaction(function () use ($request, $approval_request) {
            $validateDate = $request->validated();
            $approval_request?->approvable?->answerRequest(ApprovalStatusEnum::APPROVED, $validateDate['comment'] ?? null);
            return $request->redirect_url ? redirect($request->redirect_url) : redirect()->route('dashboard');
        });
    }

    /**
     * Reject the request.
     *
     * @param ApprovalFormRequest  $request
     * @param ApprovalRequest $approval_request
     */
    public function reject(ApprovalFormRequest $request, ApprovalRequest $approval_request)
    {
        return $this->withTransaction(function () use ($request, $approval_request) {
            $validateDate = $request->validated();
            $approval_request?->approvable?->answerRequest(ApprovalStatusEnum::REJECTED, $validateDate['comment'] ?? null);

            return $request->redirect_url ? redirect($request->redirect_url) : redirect()->route('dashboard');
        });
    }

    /**
     * Cancel the approval of the ApprovalRequest $approval_request.
     * @param ApprovalFormRequest  $request
     * @param ApprovalRequest $approval_request
     */
    public function cancelApproval(ApprovalFormRequest $request, ApprovalRequest $approval_request)
    {
        return $this->withTransaction(function () use ($request, $approval_request) {
            $approval_request?->approvable?->cancalApproval();
            return $request->redirect_url ? redirect($request->redirect_url) : redirect()->route('dashboard');
        });
    }
}
