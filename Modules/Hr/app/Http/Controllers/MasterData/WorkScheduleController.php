<?php

namespace Modules\Hr\Http\Controllers\MasterData;

use Inertia\Inertia;
use Modules\Hr\Models\WorkSchedule;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\TransactionController;
use Modules\Hr\Transformers\WorkScheduleFormResource;
use Modules\Hr\Services\MasterData\WorkScheduleService;
use Modules\Hr\Http\Requests\MasterData\WorkScheduleRequest;

class WorkScheduleController extends TransactionController
{
    private $path = 'Hr::MasterData/WorkSchedules/';

    public function __construct(private WorkScheduleService $workScheduleService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('work_schedule_access'), 403);

        return Inertia::render($this->path . 'WorkSchedulesListComponent', [
            'work_schedules'    => WorkSchedule::filter(request()->query() ?? [])->paginate(request('perPage', 10))
        ]);
    }

    /**
     * Create a new work schedule.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        abort_if(Gate::denies('work_schedule_create'), 403);

        return Inertia::render($this->path . 'WorkScheduleFormComponent', [
            'method_type'        => 'post',
            'action'             => route('hr.master-data.work-schedules.store'),
            'days'               => $this->workScheduleService->getDays()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WorkScheduleRequest $request)
    {
        abort_if(Gate::denies('work_schedule_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->workScheduleService->store($request->validated());
            return redirect()->route('hr.master-data.work-schedules.index');
        });
    }

    public function show(WorkSchedule $workSchedule)
    {
        abort_if(Gate::denies('work_schedule_show'), 403);

        return Inertia::render($this->path . 'WorkScheduleFormComponent', [
            'item'               => new WorkScheduleFormResource($workSchedule),
            'method_type'        => 'show',
            'isDisabled'         => true,
            'days'               => $this->workScheduleService->getDays()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkSchedule $workSchedule)
    {
        abort_if(Gate::denies('work_schedule_edit'), 403);

        return Inertia::render($this->path . 'WorkScheduleFormComponent', [
            'item'               => new WorkScheduleFormResource($workSchedule),
            'method_type'        => 'put',
            'action'             => route('hr.master-data.work-schedules.update', $workSchedule),
            'days'               => $this->workScheduleService->getDays()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WorkScheduleRequest $request, WorkSchedule $workSchedule)
    {
        abort_if(Gate::denies('work_schedule_edit'), 403);

        return $this->withTransaction(function () use ($request, $workSchedule) {
            $this->workScheduleService->update($workSchedule, $request->validated());
            return redirect()->route('hr.master-data.work-schedules.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkSchedule $workSchedule)
    {
        abort_if(Gate::denies('work_schedule_delete'), 403);

        return $this->withTransaction(function () use ($workSchedule) {
            $this->workScheduleService->destroy($workSchedule);
            return redirect()->route('hr.master-data.work-schedules.index');
        });
    }
}
