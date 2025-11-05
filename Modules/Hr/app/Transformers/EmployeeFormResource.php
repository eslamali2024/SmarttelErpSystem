<?php

namespace Modules\Hr\Transformers;

use Illuminate\Http\Request;
use Modules\Hr\Models\AllowanceType;
use Modules\Hr\Enums\AllowancesTypeEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeFormResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'code'             => $this->code,
            'name_ar'          => $this->name_ar,
            'name'             => $this->name,
            'email'            => $this->email,
            'phone'            => $this->phone,
            'gender'           => $this->gender?->value ?? null,
            'marital_status'   => $this->marital_status?->value ?? null,
            'dob'              => $this->dob?->format('Y-m-d'),
            'joining_date'     => $this->joining_date?->format('Y-m-d'),
            'national_id'      => $this->national_id,
            'address'          => $this->address,
            'notes'            => $this->notes,
            'contract'         => [
                'start_date'            => $this->currentContract?->start_date?->format('Y-m-d'),
                'end_date'              => $this->currentContract?->end_date?->format('Y-m-d'),
                'division_id'           => $this->currentContract?->currentPosition?->division_id,
                'department_id'         => $this->currentContract?->currentPosition?->department_id,
                'section_id'            => $this->currentContract?->currentPosition?->section_id,
                'position_id'           => $this->currentContract?->currentPosition?->position_id,
                'time_management_id'    => $this->currentContract?->time_management_id,
                'work_schedule_id'      => $this->currentContract?->work_schedule_id,
                'notes'                 => $this->currentContract?->notes
            ],
            'salary'            => array_merge($this->initializeAllowances($this->currentContract?->allowances), $this->currentContract?->salary?->toArray()),
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
