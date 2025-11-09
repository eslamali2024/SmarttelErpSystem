<?php

namespace Modules\Hr\Http\Controllers\MasterData;

use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use Modules\Hr\Models\BonusType;
use App\Http\Controllers\TransactionController;
use App\Http\Requests\ImportFileRequest;
use Modules\Hr\Services\MasterData\BonusTypeService;
use Modules\Hr\Http\Requests\MasterData\BonusTypeRequest;

class BonusTypeController extends TransactionController
{
    private $path = 'Hr::MasterData/BonusTypes/';

    public function __construct(private BonusTypeService $bonusTypeService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('bonus_type_access'), 403);

        return Inertia::render($this->path . 'BonusTypesListComponent', [
            'bonus_types'    => BonusType::filter(request()->query() ?? [])->paginate(request('perPage', 10)),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BonusTypeRequest $request)
    {
        abort_if(Gate::denies('bonus_type_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->bonusTypeService->store($request->validated());
            return redirect()->route('hr.master-data.bonus-types.index');
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BonusTypeRequest $request, BonusType $bonusType)
    {
        abort_if(Gate::denies('bonus_type_edit'), 403);

        return $this->withTransaction(function () use ($request, $bonusType) {
            $this->bonusTypeService->update($bonusType, $request->validated());
            return redirect()->route('hr.master-data.bonus-types.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BonusType $bonusType)
    {
        abort_if(Gate::denies('bonus_type_delete'), 403);

        return $this->withTransaction(function () use ($bonusType) {
            $this->bonusTypeService->destroy($bonusType);
            return redirect()->route('hr.master-data.bonus-types.index');
        });
    }

    /**
     * Import Public Holidays from an Excel file.
     *
     * @param ImportFileRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(ImportFileRequest $request)
    {
        return $this->withTransaction(function () use ($request) {
            $this->bonusTypeService->import($request->file('import_file'));
            return redirect()->route('hr.master-data.bonus-types.index');
        });
    }

    /**
     * Download a public holiday template in Excel format.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate()
    {
        return response()->download(public_path('assets/imports/bonus_type_template.xlsx'));
    }
}
