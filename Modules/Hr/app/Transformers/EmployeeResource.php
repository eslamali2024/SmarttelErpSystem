<?php

namespace Modules\Hr\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'notional_id'      => $this->national_id,
            'contract'        => [
                'start_date'       => $this->currentContract?->start_date?->format('Y-m-d'),
                'end_date'         => $this->currentContract?->end_date?->format('Y-m-d'),
                'division_id'      => $this->currentContract?->currentPosition?->division_id,
                'department_id'    => $this->currentContract?->currentPosition?->department_id,
                'section_id'       => $this->currentContract?->currentPosition?->section_id,
                'position_id'      => $this->currentContract?->currentPosition?->position_id,
            ],
        ];
    }
}
