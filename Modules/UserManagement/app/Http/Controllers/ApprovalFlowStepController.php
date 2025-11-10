<?php

namespace Modules\UserManagement\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use App\Models\Approval\ApprovalFlow;
use App\Enums\Approval\ApprovalTypeEnum;
use App\Models\Approval\ApprovalFlowStep;
use App\Http\Controllers\TransactionController;
use Modules\UserManagement\Services\ApprovalFlowStepService;
use Modules\UserManagement\Http\Requests\ApprovalFlowStepRequest;

class ApprovalFlowStepController extends TransactionController
{
    private $path = 'UserManagement::ApprovalFlowSteps/';

    public function __construct(private ApprovalFlowStepService $approvalFlowStepService) {}

    /**
     * Display a listing of the resource.
     *
     * @param  ApprovalFlow  $approvalFlow
     */
    public function index(ApprovalFlow $approvalFlow)
    {
        abort_if(Gate::denies('approval_flow_step_access'), 403);

        return Inertia::render($this->path . 'ApprovalFlowStepsListComponent', [
            'approvalFlow'        => $approvalFlow,
            'approver_types'      => ApprovalTypeEnum::items(),
            'approval_flow_steps' => ApprovalFlowStep::with('approvalFlow')->where('approval_flow_id', $approvalFlow->id)->filter(request()->query() ?? [])->paginate(request('perPage', 10)),
        ]);
    }

    /**
     * Returns a list of permissions for the create approval_flow_step form.
     *
     * @param  ApprovalFlow  $approvalFlow
     */
    public function create(ApprovalFlow $approvalFlow)
    {
        abort_if(Gate::denies('approval_flow_step_create'), 403);

        return response()->json([
            'data' => [
                ...$this->approvalFlowStepService->getAllData()
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param  ApprovalFlow  $approvalFlow
     * @param  ApprovalFlowStepRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ApprovalFlow $approvalFlow, ApprovalFlowStepRequest $request)
    {
        abort_if(Gate::denies('approval_flow_step_create'), 403);

        return $this->withTransaction(function () use ($request, $approvalFlow) {
            $this->approvalFlowStepService->store($approvalFlow, $request->validated());
            return redirect()->route('user-management.approval-flow-steps.index', ['approvalFlow' => $approvalFlow->id, 'page' => request('page', 1)]);
        });
    }

    /**
     * Update the specified resource in storage.
     * @param  ApprovalFlow  $approvalFlow
     * @param  ApprovalFlowStep  $approval_flow_step
     * @param  ApprovalFlowStepRequest  $request
     */
    public function update(ApprovalFlowStepRequest $request, ApprovalFlow $approvalFlow, ApprovalFlowStep $step)
    {
        abort_if(Gate::denies('approval_flow_step_edit'), 403);

        return $this->withTransaction(function () use ($request, $step) {
            $this->approvalFlowStepService->update($step, $request->validated());
            return redirect()->route('user-management.approval-flow-steps.index', ['approvalFlow' => $step->approval_flow_id, 'page' => request('page', 1)]);
        });
    }

    /**
     * Remove the specified resource from storage.
     * @param  ApprovalFlow  $approvalFlow
     * @param  ApprovalFlowStep  $approval_flow_step
     */
    public function destroy(ApprovalFlow $approvalFlow, ApprovalFlowStep $step)
    {
        abort_if(Gate::denies('approval_flow_step_delete'), 403);

        return $this->withTransaction(function () use ($approvalFlow, $step) {
            $this->approvalFlowStepService->destroy($step);
            return redirect()->route('user-management.approval-flow-steps.index', ['approvalFlow' => $approvalFlow->id, 'page' => request('page', 1)]);
        });
    }
}
