<?php

namespace Modules\UserManagement\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\TransactionController;
use App\Models\Approval\ApprovalFlow;
use Modules\UserManagement\Services\ApprovalFlowService;
use Modules\UserManagement\Http\Requests\ApprovalFlowRequest;

class ApprovalFlowController extends TransactionController
{
    private $path = 'UserManagement::ApprovalFlows/';

    public function __construct(private ApprovalFlowService $approvalFlowService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('approval_flow_access'), 403);

        return Inertia::render($this->path . 'ApprovalFlowsListComponent', [
            'approval_flows' => ApprovalFlow::filter(request()->query() ?? [])->paginate(request('perPage', 10)),
        ]);
    }

    /**
     * Returns a list of permissions for the create approval_flow form.
     *
     */
    public function create()
    {
        abort_if(Gate::denies('approval_flow_create'), 403);

        return response()->json([
            'models' => $this->approvalFlowService->getAllModels()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApprovalFlowRequest $request)
    {
        abort_if(Gate::denies('approval_flow_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->approvalFlowService->store($request->validated());
            return redirect()->route('user-management.approval-flows.index', ['page' => request('page', 1)]);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ApprovalFlowRequest $request, ApprovalFlow $approval_flow)
    {
        abort_if(Gate::denies('approval_flow_edit'), 403);

        return $this->withTransaction(function () use ($request, $approval_flow) {
            $this->approvalFlowService->update($approval_flow, $request->validated());
            return redirect()->route('user-management.approval-flows.index', ['page' => request('page', 1)]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApprovalFlow $approval_flow)
    {
        abort_if(Gate::denies('approval_flow_delete'), 403);

        return $this->withTransaction(function () use ($approval_flow) {
            $this->approvalFlowService->destroy($approval_flow);
            return redirect()->route('user-management.approval-flows.index');
        });
    }
}
