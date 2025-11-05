<?php

namespace Modules\Hr\Http\Controllers\MasterData;

use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use Modules\Hr\Models\AllowanceType;
use Modules\Hr\Enums\AllowancesTypeEnum;
use Modules\Hr\Enums\AllowancesTaxableEnum;
use App\Http\Controllers\TransactionController;
use Modules\Hr\Services\MasterData\AllowanceTypeService;
use Modules\Hr\Http\Requests\MasterData\AllowanceTypeRequest;

class AllowanceTypeController extends TransactionController
{
    private $path = 'Hr::MasterData/AllowanceTypes/';

    public function __construct(private AllowanceTypeService $allowanceTypeService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('allowance_type_access'), 403);

        return Inertia::render($this->path . 'AllowanceTypesListComponent', [
            'allowance_types'    => AllowanceType::filter(request()->query() ?? [])->paginate(request('perPage', 10)),
            'types'              => AllowancesTypeEnum::items(),
            'taxables'           => AllowancesTaxableEnum::items(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AllowanceTypeRequest $request)
    {
        abort_if(Gate::denies('allowance_type_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->allowanceTypeService->store($request->validated());
            return redirect()->route('hr.master-data.allowance-types.index');
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AllowanceTypeRequest $request, AllowanceType $allowanceType)
    {
        abort_if(Gate::denies('allowance_type_edit'), 403);

        return $this->withTransaction(function () use ($request, $allowanceType) {
            $this->allowanceTypeService->update($allowanceType, $request->validated());
            return redirect()->route('hr.master-data.allowance-types.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AllowanceType $allowanceType)
    {
        abort_if(Gate::denies('allowance_type_delete'), 403);

        return $this->withTransaction(function () use ($allowanceType) {
            $this->allowanceTypeService->destroy($allowanceType);
            return redirect()->route('hr.master-data.allowance-types.index');
        });
    }
}
