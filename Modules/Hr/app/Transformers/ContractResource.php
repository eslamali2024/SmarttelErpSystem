<?php

namespace Modules\Hr\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'contract' => [
                'start_date'            => $this?->start_date?->format('Y-m-d'),
                'end_date'              => $this?->end_date?->format('Y-m-d'),
                'division'              => $this?->currentPosition?->division?->name,
                'department'            => $this?->currentPosition?->department?->name,
                'section'               => $this?->currentPosition?->section?->name,
                'position'              => $this?->currentPosition?->position?->name,
                'time_management'       => $this?->timeManagement?->name,
                'work_schedule'         => $this?->workSchedule?->name,
                'notes'                 => $this?->notes
            ],
            'employee' => [
                'id'                    => $this->employee?->id,
                'code'                  => $this->employee?->code,
                'name_ar'               => $this->employee?->name_ar,
                'name'                  => $this->employee?->name,
                'email'                 => $this->employee?->email,
                'phone'                 => $this->employee?->phone,
                'gender'                => $this->employee?->gender?->value ?? null,
                'marital_status'        => $this->employee?->marital_status?->value ?? null,
                'dob'                   => $this->employee?->dob?->format('Y-m-d'),
                'joining_date'          => $this->employee?->joining_date?->format('Y-m-d'),
                'national_id'           => $this->employee?->national_id,
                'address'               => $this->employee?->address,
                'notes'                 => $this->employee?->notes,
            ]
        ];
    }
}
