<?php

namespace Modules\Hr\Http\Controllers;

use Inertia\Inertia;
use Modules\Hr\Models\Bonus;
use Modules\Hr\Models\BonusType;
use Illuminate\Support\Facades\Gate;
use Modules\Hr\Services\BonusService;
use App\Enums\Approval\ApprovalStatusEnum;
use Modules\Hr\Http\Requests\BonusRequest;
use App\Http\Controllers\TransactionController;

class BonusController extends TransactionController
{
    private $path = 'Hr::Bonuses/';

    public function __construct(private BonusService $bonusService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('bonus_access'), 403);

        return Inertia::render($this->path . 'BonusesListComponent', [
            'bonuses'    => Bonus::with([
                'employee:id,name',
                'bonusType:id,name',
                'createdBy:id,name',
            ])->filter(request()->query() ?? [])->paginate(request('perPage', 10)),
            'statuses'   => ApprovalStatusEnum::items(),
        ]);
    }

    /**
     * Return a JSON response containing the list of employees and bonus types
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        return response()->json([
            'employees'     => $this->bonusService->employees(),
            'bonus_types'   => BonusType::pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BonusRequest $request)
    {
        abort_if(Gate::denies('bonus_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->bonusService->store($request->validated());
            return $request->redirect_url ? redirect($request->redirect_url) : redirect()->route('hr.bonuses.index');
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \Modules\Hr\Models\Bonus $bonus
     * @return \Inertia\Inertia
     */
    public function show(Bonus $bonus)
    {
        abort_if(Gate::denies('bonus_show'), 403);
        $bonus->load(['employee', 'bonusType', 'createdBy']);

        return Inertia::render($this->path . 'BonusShowComponent', [
            'bonus' => $bonus,
            ...$bonus->getApproval(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BonusRequest $request, Bonus $bonus)
    {
        abort_if(Gate::denies('bonus_edit'), 403);

        return $this->withTransaction(function () use ($request, $bonus) {
            $this->bonusService->update($bonus, $request->validated());
            return $request->redirect_url ? redirect($request->redirect_url) : redirect()->route('hr.bonuses.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bonus $bonus)
    {
        abort_if(Gate::denies('bonus_delete'), 403);

        return $this->withTransaction(function () use ($bonus) {
            $this->bonusService->destroy($bonus);
            return request()->redirect_url ? redirect(request()->redirect_url) : redirect()->route('hr.bonuses.index');
        });
    }
}
