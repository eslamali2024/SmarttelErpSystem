<?php

namespace Modules\Hr\Http\Controllers\MasterData;

use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use Modules\Hr\Models\DeductionType;
use App\Http\Requests\ImportFileRequest;
use App\Http\Controllers\TransactionController;
use Modules\Hr\Services\MasterData\DeductionTypeService;
use Modules\Hr\Http\Requests\MasterData\DeductionTypeRequest;

class DeductionTypeController extends TransactionController
{
    private $path = 'Hr::MasterData/DeductionTypes/';

    public function __construct(private DeductionTypeService $deductionTypeService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('deduction_type_access'), 403);

        return Inertia::render($this->path . 'DeductionTypesListComponent', [
            'deduction_types'    => DeductionType::filter(request()->query() ?? [])->paginate(request('perPage', 10)),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DeductionTypeRequest $request)
    {
        abort_if(Gate::denies('deduction_type_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->deductionTypeService->store($request->validated());
            return redirect()->route('hr.master-data.deduction-types.index');
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DeductionTypeRequest $request, DeductionType $deductionType)
    {
        abort_if(Gate::denies('deduction_type_edit'), 403);

        return $this->withTransaction(function () use ($request, $deductionType) {
            $this->deductionTypeService->update($deductionType, $request->validated());
            return redirect()->route('hr.master-data.deduction-types.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeductionType $deductionType)
    {
        abort_if(Gate::denies('deduction_type_delete'), 403);

        return $this->withTransaction(function () use ($deductionType) {
            $this->deductionTypeService->destroy($deductionType);
            return redirect()->route('hr.master-data.deduction-types.index');
        });
    }

    /**
     * Import  Deduction Type from an Excel file.
     *
     * @param ImportFileRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(ImportFileRequest $request)
    {
        return $this->withTransaction(function () use ($request) {
            $this->deductionTypeService->import($request->file('import_file'));
            return redirect()->route('hr.master-data.deduction-types.index');
        });
    }

    /**
     * Download a Deduction Type template in Excel format.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate()
    {
        return response()->download(public_path('assets/imports/deduction_type_template.xlsx'));
    }
}
