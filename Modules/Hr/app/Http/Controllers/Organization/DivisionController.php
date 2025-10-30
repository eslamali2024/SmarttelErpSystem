<?php

namespace Modules\Hr\Http\Controllers\Organization;

use App\Models\User;
use Inertia\Inertia;
use Modules\Hr\Models\Division;
use App\Http\Controllers\TransactionController;
use Modules\Hr\Services\DivisionService;
use Modules\Hr\Http\Requests\DivisionRequest;

class DivisionController extends TransactionController
{
    private $path = 'Hr::Organization/Divisions/';

    public function __construct(private DivisionService $divisionService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        return $this->withTransaction(function () use ($division) {
            $this->divisionService->destroy($division);
            return redirect()->route('hr.organization.divisions.index');
        });
    }
}
