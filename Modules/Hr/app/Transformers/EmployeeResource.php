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
            'avatar'           => $this->avatar,
            'email'            => $this->email,
            'phone'            => $this->phone,
            'gender_label'           => $this->gender?->label() ?? null,
            'marital_status_label'   => $this->marital_status?->label() ?? null,
            'dob'              => $this->dob?->format('Y-m-d'),
            'joining_date'     => $this->joining_date?->format('Y-m-d'),
            'national_id'      => $this->national_id,
            'address'          => $this->address,
            'notes'            => $this->notes,
            'contract'         => [
                'start_date'            => $this->currentContract?->start_date?->format('Y-m-d'),
                'end_date'              => $this->currentContract?->end_date?->format('Y-m-d'),
                'division'              => $this->currentContract?->currentPosition?->division?->name,
                'department'            => $this->currentContract?->currentPosition?->department?->name,
                'section'               => $this->currentContract?->currentPosition?->section?->name,
                'position'              => $this->currentContract?->currentPosition?->position?->name,
                'time_management'       => $this->currentContract?->timeManagement?->name,
                'work_schedule'         => $this->currentContract?->workSchedule?->name,
                'notes'                 => $this->currentContract?->notes
            ],
        ];
    }
}
