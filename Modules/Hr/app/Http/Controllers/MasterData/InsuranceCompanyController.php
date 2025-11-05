<?php

namespace Modules\Hr\Http\Controllers\MasterData;

use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use Modules\Hr\Models\InsuranceCompany;
use App\Http\Controllers\TransactionController;
use Modules\Hr\Services\MasterData\InsuranceCompanyService;
use Modules\Hr\Http\Requests\MasterData\InsuranceCompanyRequest;

class InsuranceCompanyController extends TransactionController
{
    private $path = 'Hr::MasterData/InsuranceCompanies/';

    public function __construct(private InsuranceCompanyService $insuranceCompanyService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('insurance_company_access'), 403);

        return Inertia::render($this->path . 'InsuranceCompaniesListComponent', [
            'insurance_companies'    => InsuranceCompany::filter(request()->query() ?? [])->paginate(request('perPage', 10))
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InsuranceCompanyRequest $request)
    {
        abort_if(Gate::denies('insurance_company_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->insuranceCompanyService->store($request->validated());
            return redirect()->route('hr.master-data.insurance-companies.index');
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InsuranceCompanyRequest $request, InsuranceCompany $insuranceCompany)
    {
        abort_if(Gate::denies('insurance_company_edit'), 403);

        return $this->withTransaction(function () use ($request, $insuranceCompany) {
            $this->insuranceCompanyService->update($insuranceCompany, $request->validated());
            return redirect()->route('hr.master-data.insurance-companies.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InsuranceCompany $insuranceCompany)
    {
        abort_if(Gate::denies('insurance_company_delete'), 403);

        return $this->withTransaction(function () use ($insuranceCompany) {
            $this->insuranceCompanyService->destroy($insuranceCompany);
            return redirect()->route('hr.master-data.insurance-companies.index');
        });
    }
}
