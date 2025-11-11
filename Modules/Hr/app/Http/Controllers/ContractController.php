<?php

namespace Modules\Hr\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use Modules\Hr\Models\EmployeeContract;
use Modules\Hr\Services\ContractService;
use Modules\Hr\Http\Requests\ContractRequest;
use Modules\Hr\Transformers\ContractResource;
use App\Http\Controllers\TransactionController;
use Modules\Hr\Enums\ContractStatusEnum;
use Modules\Hr\Transformers\ContractFormResource;

class ContractController extends TransactionController
{
    private $path = 'Hr::Contracts/';

    public function __construct(private ContractService $contractService) {}


    /**
     * Create a new contract.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        abort_if(Gate::denies('employee_contract_create'), 403);

        return Inertia::render($this->path . 'ContractFormComponent', [
            'method_type'        => 'post',
            'action'             => route('hr.contracts.store'),
            ...$this->contractService->getInitilizeData()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContractRequest $request)
    {
        abort_if(Gate::denies('employee_contract_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->contractService->store($request->validated());
            return $request->redirect_url ? redirect($request->redirect_url) : redirect()->route('hr.employees.activity.contracts', ['employee' => $request->employee_id]);
        });
    }

    /**
     * Show the details of the given contract.
     *
     * @param  \Modules\Hr\Models\EmployeeContract  $contract
     * @return \Inertia\Response
     */
    public function show(EmployeeContract $contract)
    {
        abort_if(Gate::denies('employee_contract_show'), 403);

        return Inertia::render($this->path . 'ContractShowComponent', [
            'contract'              => new ContractResource($contract),
        ]);
    }

    /**
     * Show the form for editing the given contract.
     *
     * @param  \Modules\Hr\Models\EmployeeContract  $contract
     * @return \Inertia\Response
     */
    public function edit(EmployeeContract $contract)
    {
        abort_if(Gate::denies('employee_contract_create'), 403);
        $contract->load([
            'currentPosition',
            'salary'
        ]);

        return Inertia::render($this->path . 'ContractFormComponent', [
            'item'               => new ContractFormResource($contract),
            'method_type'        => 'put',
            'action'             => route('hr.contracts.update', $contract),
            ...$this->contractService->getInitilizeData()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContractRequest $request, EmployeeContract $contract)
    {
        abort_if(Gate::denies('employee_contract_edit'), 403);

        return $this->withTransaction(function () use ($request, $contract) {
            $this->contractService->update($contract, $request->validated());
            return $request->redirect_url ? redirect($request->redirect_url) : redirect()->route('hr.employees.activity.contracts', ['employee' => $contract->employee_id]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeContract $contract)
    {
        abort_if(Gate::denies('employee_contract_delete'), 403);

        return $this->withTransaction(function () use ($contract) {
            $this->contractService->destroy($contract);
            return request()->redirect_url ? redirect(request()->redirect_url) : redirect()->route('hr.employees.activity.contracts', ['employee' => $contract->employee_id]);
        });
    }

    public function changeStatus(EmployeeContract $contract, int $status)
    {
        abort_if(Gate::denies('employee_contract_edit'), 403);

        return $this->withTransaction(function () use ($contract, $status) {
            $this->contractService->changeStatus($contract, $status);
            return request()->redirect_url ? redirect(request()->redirect_url) : redirect()->route('hr.employees.activity.contracts', ['employee' => $contract->employee_id]);
        });
    }
}
