<?php

namespace Modules\Hr\Http\Controllers\MasterData;

use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use Modules\Hr\Models\TimeManagement;
use App\Http\Controllers\TransactionController;
use Modules\Hr\Services\MasterData\TimeManagementService;
use Modules\Hr\Http\Requests\MasterData\TimeManagementRequest;

class TimeManagementController extends TransactionController
{
    private $path = 'Hr::MasterData/TimeManagements/';

    public function __construct(private TimeManagementService $timeManagementService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('time_management_access'), 403);

        return Inertia::render($this->path . 'TimeManagementsListComponent', [
            'time_managements'    => TimeManagement::filter(request()->query() ?? [])->paginate(request('perPage', 10))
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TimeManagementRequest $request)
    {
        abort_if(Gate::denies('time_management_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->timeManagementService->store($request->validated());
            return redirect()->route('hr.master-data.time-managements.index');
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TimeManagementRequest $request, TimeManagement $timeManagement)
    {
        abort_if(Gate::denies('time_management_edit'), 403);

        return $this->withTransaction(function () use ($request, $timeManagement) {
            $this->timeManagementService->update($timeManagement, $request->validated());
            return redirect()->route('hr.master-data.time-managements.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeManagement $timeManagement)
    {
        abort_if(Gate::denies('time_management_delete'), 403);

        return $this->withTransaction(function () use ($timeManagement) {
            $this->timeManagementService->destroy($timeManagement);
            return redirect()->route('hr.master-data.time-managements.index');
        });
    }
}
