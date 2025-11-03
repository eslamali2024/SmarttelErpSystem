<?php

namespace Modules\Hr\Http\Controllers\Organization;

use Inertia\Inertia;
use Modules\Hr\Models\Section;
use Modules\Hr\Models\Division;
use Modules\Hr\Models\Position;
use Modules\Hr\Models\Department;
use Modules\Hr\Services\PositionService;
use Modules\Hr\Http\Requests\PositionRequest;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Gate;

class PositionController extends TransactionController
{
    private $path = 'Hr::Organization/Positions/';

    public function __construct(private PositionService $positionService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('position_access'), 403);

        return Inertia::render($this->path . 'PositionsListComponent', [
            'positions'     => Position::with(['department', 'division', 'section'])->filter(request()->query() ?? [])->paginate(10),
            'divisions'     => Division::pluck('name', 'id'),
            'departments'   => Department::get(['id', 'name', 'division_id']),
            'sections'      => Section::get(['id', 'name', 'department_id']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PositionRequest $request)
    {
        abort_if(Gate::denies('position_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->positionService->store($request->validated());
            return redirect()->route('hr.organization.positions.index');
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PositionRequest $request, Position $position)
    {
        abort_if(Gate::denies('position_edit'), 403);

        return $this->withTransaction(function () use ($request, $position) {
            $this->positionService->update($position, $request->validated());
            return redirect()->route('hr.organization.positions.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        abort_if(Gate::denies('position_delete'), 403);

        return $this->withTransaction(function () use ($position) {
            $this->positionService->destroy($position);
            return redirect()->route('hr.organization.positions.index');
        });
    }
}
