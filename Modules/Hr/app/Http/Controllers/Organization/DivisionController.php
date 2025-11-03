<?php

namespace Modules\Hr\Http\Controllers\Organization;

use App\Models\User;
use Inertia\Inertia;
use Modules\Hr\Models\Division;
use Illuminate\Support\Facades\Gate;
use Modules\Hr\Services\DivisionService;
use Modules\Hr\Http\Requests\DivisionRequest;
use App\Http\Controllers\TransactionController;

class DivisionController extends TransactionController
{
    private $path = 'Hr::Organization/Divisions/';

    public function __construct(private DivisionService $divisionService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('division_access'), 403);

        return Inertia::render($this->path . 'DivisionsListComponent', [
            'divisions'     => Division::with('manager')->filter(request()->query() ?? [])->paginate(10),
            'managers'      => User::pluck('name', 'id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DivisionRequest $request)
    {
        abort_if(Gate::denies('division_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->divisionService->store($request->validated());
            return redirect()->route('hr.organization.divisions.index');
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DivisionRequest $request, Division $division)
    {
        abort_if(Gate::denies('division_edit'), 403);

        return $this->withTransaction(function () use ($request, $division) {
            $this->divisionService->update($division, $request->validated());
            return redirect()->route('hr.organization.divisions.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Division $division)
    {
        abort_if(Gate::denies('division_delete'), 403);

        return $this->withTransaction(function () use ($division) {
            $this->divisionService->destroy($division);
            return redirect()->route('hr.organization.divisions.index');
        });
    }
}
