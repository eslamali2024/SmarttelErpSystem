<?php

namespace Modules\Hr\Http\Controllers;

use Inertia\Inertia;
use Modules\Hr\Models\Deduction;
use Illuminate\Support\Facades\Gate;
use Modules\Hr\Models\DeductionType;
use Modules\Hr\Services\DeductionService;
use App\Enums\Approval\ApprovalStatusEnum;
use Modules\Hr\Http\Requests\DeductionRequest;
use App\Http\Controllers\TransactionController;

class DeductionController extends TransactionController
{
    private $path = 'Hr::Deductions/';

    public function __construct(private DeductionService $deductionService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('deduction_access'), 403);

        return Inertia::render($this->path . 'DeductionsListComponent', [
            'deductions'    => Deduction::with([
                'employee:id,name',
                'deductionType:id,name',
                'createdBy:id,name',
            ])->filter(request()->query() ?? [])->paginate(request('perPage', 10)),
            'statuses'   => ApprovalStatusEnum::items(),
        ]);
    }

    /**
     * Return a JSON response containing the list of employees and deduction types
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        return response()->json([
            'employees'     => $this->deductionService->employees(),
            'deduction_types'   => DeductionType::pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DeductionRequest $request)
    {
        abort_if(Gate::denies('deduction_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->deductionService->store($request->validated());
            return $request->redirect_url ? redirect($request->redirect_url) : redirect()->route('hr.deductions.index');
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \Modules\Hr\Models\Deduction $deduction
     * @return \Inertia\Inertia
     */
    public function show(Deduction $deduction)
    {
        abort_if(Gate::denies('deduction_show'), 403);
        $deduction->load(['employee', 'deductionType', 'createdBy']);

        return Inertia::render($this->path . 'DeductionShowComponent', [
            'deduction' => $deduction,
            ...$deduction->getApproval(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DeductionRequest $request, Deduction $deduction)
    {
        abort_if(Gate::denies('deduction_edit'), 403);

        return $this->withTransaction(function () use ($request, $deduction) {
            $this->deductionService->update($deduction, $request->validated());
            return $request->redirect_url ? redirect($request->redirect_url) : redirect()->route('hr.deductions.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deduction $deduction)
    {
        abort_if(Gate::denies('deduction_delete'), 403);

        return $this->withTransaction(function () use ($deduction) {
            $this->deductionService->destroy($deduction);
            return request()->redirect_url ? redirect(request()->redirect_url) : redirect()->route('hr.deductions.index');
        });
    }
}
