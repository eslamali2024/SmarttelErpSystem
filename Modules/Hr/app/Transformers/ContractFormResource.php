<?php

namespace Modules\Hr\Transformers;

use Illuminate\Http\Request;
use Modules\Hr\Models\AllowanceType;
use Modules\Hr\Enums\AllowancesTypeEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'contract'         => [
                'employee_id'           => $this->employee_id,
                'start_date'            => $this->start_date?->format('Y-m-d'),
                'end_date'              => $this->end_date?->format('Y-m-d'),
                'division_id'           => $this->currentPosition?->division_id,
                'department_id'         => $this->currentPosition?->department_id,
                'section_id'            => $this->currentPosition?->section_id,
                'position_id'           => $this->currentPosition?->position_id,
                'time_management_id'    => $this->time_management_id,
                'work_schedule_id'      => $this->work_schedule_id,
                'notes'                 => $this->notes
            ],
            'salary'            => array_merge($this->initializeAllowances($this->allowances), $this->salary?->toArray()),
        ];
    }

    /**
     * Initialize the allowances array.
     *
     * This function takes the allowances from the contract and formats them into
     * an array of off-cycle and recurring allowances.
     *
     * @param  array  $allowances  The allowances from the contract.
     * @return array  An array containing the formatted off-cycle and recurring allowances.
     */
    private function initializeAllowances($allowances): array
    {
        $all_allowances = AllowanceType::all();

        $formatAllowance = $allowances->mapWithKeys(function ($allowance) {
            return [$allowance->allowance_id => $allowance->amount];
        });

        $offCycle = $all_allowances->where('type', AllowancesTypeEnum::OFF_CYCLE)
            ->mapWithKeys(function ($type) use ($formatAllowance) {
                return [
                    $type->id => $formatAllowance[$type->id] ?? 0,
                ];
            });

        $recurring = $all_allowances->where('type', AllowancesTypeEnum::RECURRING)
            ->mapWithKeys(function ($type) use ($formatAllowance) {
                return [
                    $type->id => $formatAllowance[$type->id] ?? 0,
                ];
            });


        return [
            'off_cycle_allowances' => $offCycle,
            'recurring_allowances' => $recurring,
        ];
    }
}
