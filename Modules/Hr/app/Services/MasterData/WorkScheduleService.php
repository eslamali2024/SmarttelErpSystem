<?php

namespace Modules\Hr\Services\MasterData;

use Modules\Hr\Models\WorkSchedule;
use Modules\Hr\Enums\DaysInWeekEnum;
use Modules\Hr\Enums\WorkScheduleHolidayStatusEnum;
use Modules\Hr\Models\WorkScheduleRule;

class WorkScheduleService
{
    /**
     * Create a new errand type.
     *
     * @param array $data
     * @return WorkSchedule
     */
    public function store(array $data): WorkSchedule
    {
        $workSchedule = WorkSchedule::create($data);
        $this->syncWorkScheduleDays($workSchedule, $data);
        $this->syncWorkScheduleRules($workSchedule, $data);

        return $workSchedule;
    }

    /**
     * Update a work schedule.
     *
     * @param \Modules\Hr\Models\WorkSchedule $workSchedule
     * @param array $data
     * @return bool
     */
    public function update(WorkSchedule $workSchedule, array $data): bool
    {
        $this->syncWorkScheduleDays($workSchedule, $data);
        $this->syncWorkScheduleRules($workSchedule, $data);

        return $workSchedule->update($data);
    }

    /**
     * Sync the work schedule days with the given data.
     *
     * @param \Modules\Hr\Models\WorkSchedule $workSchedule
     * @param array $data
     */
    private function syncWorkScheduleDays(WorkSchedule $workSchedule, array $data)
    {
        if (isset($data['days'])) {
            $workSchedule->days()->delete();

            foreach ($data['days'] as $day) {
                $status = $day['status']
                    ? WorkScheduleHolidayStatusEnum::WORKING
                    : WorkScheduleHolidayStatusEnum::HOLIDAY;

                $workSchedule->days()->updateOrCreate(
                    ['day'    => $day['day']],
                    ['status' => $status]
                );
            }
        }
    }

    /**
     * Synchronizes the work schedule rules with the given data.
     *
     * This method iterates over the given rules and creates or updates
     * a WorkScheduleRule with the given data.
     *
     * @param \Modules\Hr\Models\WorkSchedule $workSchedule
     * @param array $data
     */
    private function syncWorkScheduleRules(WorkSchedule $workSchedule, array $data)
    {
        $workSchedule->rules()->delete();

        if (isset($data['rules'])) {
            foreach ($data['rules'] as $rule) {
                WorkScheduleRule::create(
                    [
                        'work_schedule_id'      => $workSchedule->id,
                        'deducation_after_time' => $rule['deducation_after_time'],
                        'deducation_percentage' => $rule['deducation_percentage'],
                    ]
                );
            }
        }
    }

    /**
     * Delete a WorkSchedule.
     *
     * @param \Modules\Hr\Models\WorkSchedule $workSchedule
     * @return bool
     */
    public function destroy(WorkSchedule $workSchedule): bool
    {
        return $workSchedule->delete();
    }

    /**
     * Returns a collection of work schedule days with their status set to 'Working'.
     *
     * This method returns an array of objects with the following properties:
     * - day: The day of the week as an integer (1-7).
     * - status: The status of the work schedule day as a WorkScheduleHolidayStatusEnum.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDays()
    {
        return collect(DaysInWeekEnum::getDays())->map(function ($day) {
            return (object)[
                'day'       => $day,
                'name'      => $day->label(),
                'status'    => true
            ];
        });
    }
}
