<?php

namespace Modules\Hr\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Hr\Enums\WorkScheduleHolidayStatusEnum;

class WorkScheduleFormResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'start_time'            => $this->start_time,
            'end_time'              => $this->end_time,
            'allow_late_sign_in'    => $this->allow_late_sign_in,
            'allow_early_sign_out'  => $this->allow_early_sign_out,
            'days'                  => $this->days->map(function ($day) {
                return [
                    'day'       => $day->day,
                    'name'      => $day->day?->label() ?? '',
                    'status'    => $day->status == WorkScheduleHolidayStatusEnum::WORKING ? true : false,
                ];
            }),
            'rules'                 => $this->rules->map(function ($rule) {
                return [
                    'id'                     => $rule->id,
                    'deducation_after_time'  => $rule->deducation_after_time,
                    'deducation_percentage'  => $rule->deducation_percentage,
                ];
            })
        ];
    }
}
