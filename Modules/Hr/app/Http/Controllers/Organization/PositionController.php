<?php

namespace Modules\Hr\Http\Controllers\Organization;

use Inertia\Inertia;
use Modules\Hr\Models\Position;
use Modules\Hr\Models\Department;
use Modules\Hr\Services\PositionService;
use Modules\Hr\Http\Requests\PositionRequest;
use App\Http\Controllers\TransactionController;

class PositionController extends TransactionController
{
    private $path = 'Hr::Organization/Positions/';

    public function __construct(private PositionService $positionService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->query();
        unset($search['page']);

        return Inertia::render($this->path . 'PositionsListComponent', [
            'positions' => Position::with('department')->filter($search ?? [])->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render($this->path . 'PositionFormComponent', [
            'method_type' => 'post',
            'action'      => route('hr.organization.positions.store'),
            'departments' => Department::pluck('name', 'id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PositionRequest $request)
    {
        return $this->withTransaction(function () use ($request) {
            $this->positionService->store($request->validated());
            return redirect()->route('hr.organization.positions.index');
        });
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('hr::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Position $position)
    {
        return Inertia::render($this->path . 'PositionFormComponent', [
            'method_type'   => 'put',
            'action'        => route('hr.organization.positions.update', $position->id),
            'position'      => $position,
            'departments'   => Department::pluck('name', 'id'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PositionRequest $request, Position $position)
    {
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
        return $this->withTransaction(function () use ($position) {
            $this->positionService->destroy($position);
            return redirect()->route('hr.organization.positions.index');
        });
    }
}
