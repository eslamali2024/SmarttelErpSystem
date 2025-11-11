<?php

namespace Modules\Hr\Http\Controllers\MasterData;

use Inertia\Inertia;
use Termwind\Components\Hr;
use Modules\Hr\Models\LeaveType;
use Illuminate\Support\Facades\Gate;
use Modules\Hr\Enums\LeaveTypeUnitEnum;
use App\Http\Requests\ImportFileRequest;
use Modules\Hr\Enums\LeaveTypeDurationEnum;
use App\Http\Controllers\TransactionController;
use Modules\Hr\Services\MasterData\LeaveTypeService;
use Modules\Hr\Http\Requests\MasterData\LeaveTypeRequest;

class LeaveTypeController extends TransactionController
{
    private $path = 'Hr::MasterData/LeaveTypes/';

    public function __construct(private LeaveTypeService $leaveTypeService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('leave_type_access'), 403);

        return Inertia::render($this->path . 'LeaveTypesListComponent', [
            'leave_types'    => LeaveType::filter(request()->query() ?? [])->paginate(request('perPage', 10)),
            'units'          => LeaveTypeUnitEnum::items(),
            'durations'      => LeaveTypeDurationEnum::items()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LeaveTypeRequest $request)
    {
        abort_if(Gate::denies('leave_type_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->leaveTypeService->store($request->validated());
            return redirect()->route('hr.master-data.leave-types.index');
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LeaveTypeRequest $request, LeaveType $leaveType)
    {
        abort_if(Gate::denies('leave_type_edit'), 403);

        return $this->withTransaction(function () use ($request, $leaveType) {
            $this->leaveTypeService->update($leaveType, $request->validated());
            return redirect()->route('hr.master-data.leave-types.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveType $leaveType)
    {
        abort_if(Gate::denies('leave_type_delete'), 403);

        return $this->withTransaction(function () use ($leaveType) {
            $this->leaveTypeService->destroy($leaveType);
            return redirect()->route('hr.master-data.leave-types.index');
        });
    }

    /**
     * Import Leave Types from an Excel file.
     *
     * @param ImportFileRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(ImportFileRequest $request)
    {
        return $this->withTransaction(function () use ($request) {
            $this->leaveTypeService->import($request->file('import_file'));
            return redirect()->route('hr.master-data.leave-types.index');
        });
    }

    /**
     * Download a Leave Type template in Excel format.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate()
    {
        return response()->download(public_path('assets/imports/leave_type_template.xlsx'));
    }
}
