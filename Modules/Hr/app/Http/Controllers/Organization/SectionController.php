<?php

namespace Modules\Hr\Http\Controllers\Organization;

use App\Models\User;
use Inertia\Inertia;
use Modules\Hr\Models\Section;
use Modules\Hr\Models\Division;
use Modules\Hr\Models\Department;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\TransactionController;
use Modules\Hr\Services\Organization\SectionService;
use Modules\Hr\Http\Requests\Organization\SectionRequest;

class SectionController extends TransactionController
{
    private $path = 'Hr::Organization/Sections/';

    public function __construct(private SectionService $sectionService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('section_access'), 403);

        return Inertia::render($this->path . 'SectionsListComponent', [
            'sections'    => Section::with(['manager', 'division', 'department'])->filter(request()->query() ?? [])->paginate(10),
            'divisions'   => Division::pluck('name', 'id'),
            'departments' => Department::get(['id', 'name', 'division_id']),
            'managers'    => User::pluck('name', 'id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SectionRequest $request)
    {
        abort_if(Gate::denies('section_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->sectionService->store($request->validated());
            return redirect()->route('hr.organization.sections.index');
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SectionRequest $request, Section $section)
    {
        abort_if(Gate::denies('section_edit'), 403);

        return $this->withTransaction(function () use ($request, $section) {
            $this->sectionService->update($section, $request->validated());
            return redirect()->route('hr.organization.sections.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section)
    {
        abort_if(Gate::denies('section_delete'), 403);

        return $this->withTransaction(function () use ($section) {
            $this->sectionService->destroy($section);
            return redirect()->route('hr.organization.sections.index');
        });
    }
}
