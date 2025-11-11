<?php

namespace Modules\Hr\Imports;

use Illuminate\Support\Collection;
use Modules\Hr\Models\WorkSchedule;
use Modules\Hr\Enums\DaysInWeekEnum;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Modules\Hr\Enums\WorkScheduleHolidayStatusEnum;



class WorkScheduleImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if (!empty($row) && isset($row['name']) && !is_null($row['name'])) {

                // Create or update work schedule
                $workSchedule = WorkSchedule::updateOrCreate(
                    ['name' => $row['name']],
                    [
                        'name' => $row['name'],
                        'start_time' => $this->timeFormatter($row['start_time'] ?? null),
                        'end_time' => $this->timeFormatter($row['end_time'] ?? null),
                        'allow_late_sign_in' => $this->timeFormatter($row['allow_late_sign_in'] ?? null),
                        'allow_early_sign_out' => $this->timeFormatter($row['allow_early_sign_out'] ?? null),
                    ]
                );

                // dd($workSchedule, $row->toArray());
                // Handle work schedule days
                $this->handleWorkScheduleDays($workSchedule, $row->toArray());

                // Handle work schedule rules
                $this->handleWorkScheduleRules($workSchedule, $row->toArray());
            }
        }
    }

    /**
     * Handle work schedule days creation
     */
    private function handleWorkScheduleDays(WorkSchedule $workSchedule, array $row)
    {
        // Clear existing days
        $workSchedule->days()->delete();

        // Check if it's a rotational schedule
        if (isset($row['rotation_holiday']) && strtolower($row['rotation_holiday']) === 'yes') {
            $workSchedule->days()->create([
                'day' => DaysInWeekEnum::ROTATIONHOLIDAY->value,
                'status' => WorkScheduleHolidayStatusEnum::HOLIDAY,
            ]);
        } else {
            // Create days for each day of the week
            $days = [
                'sunday' => DaysInWeekEnum::SUNDAY,
                'monday' => DaysInWeekEnum::MONDAY,
                'tuesday' => DaysInWeekEnum::TUESDAY,
                'wednesday' => DaysInWeekEnum::WEDNESDAY,
                'thursday' => DaysInWeekEnum::THURSDAY,
                'friday' => DaysInWeekEnum::FRIDAY,
                'saturday' => DaysInWeekEnum::SATURDAY,
            ];

            foreach ($days as $dayKey => $dayEnum) {
                $isWorking = isset($row[$dayKey]) &&
                    (strtolower($row[$dayKey]) === 'work' || strtolower($row[$dayKey]) === 'working' || $row[$dayKey] === '1');

                $status = $isWorking ? WorkScheduleHolidayStatusEnum::WORKING : WorkScheduleHolidayStatusEnum::HOLIDAY;

                $workSchedule->days()->create([
                    'day' => $dayEnum->value,
                    'status' => $status,
                ]);
            }
        }
    }

    /**
     * Handle work schedule rules creation
     */
    private function handleWorkScheduleRules(WorkSchedule $workSchedule, array $row)
    {
        // Clear existing rules
        $workSchedule->rules()->delete();

        // Check for rule columns in the Excel file
        if (isset($row['deduction_after_time']) && isset($row['deduction_percentage'])) {
            $workSchedule->rules()->create([
                'deducation_after_time' => (int) $row['deduction_after_time'],
                'deducation_percentage' => (float) $row['deduction_percentage'],
            ]);
        }

        // Handle multiple rules if they exist (rule_1, rule_2, etc.)
        $ruleIndex = 1;
        while (isset($row["deduction_after_time_{$ruleIndex}"]) || isset($row["deduction_percentage_{$ruleIndex}"])) {
            if (!empty($row["deduction_after_time_{$ruleIndex}"]) && !empty($row["deduction_percentage_{$ruleIndex}"])) {
                $workSchedule->rules()->create([
                    'deducation_after_time' => (int) $row["deduction_after_time_{$ruleIndex}"],
                    'deducation_percentage' => (float) $row["deduction_percentage_{$ruleIndex}"],
                ]);
            }
            $ruleIndex++;
        }
    }

    /**
     * Format time from Excel
     */
    private function timeFormatter($timeValue)
    {
        if (empty($timeValue)) {
            return null;
        }

        // If it's already in HH:MM format
        if (is_string($timeValue) && preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $timeValue)) {
            return substr($timeValue, 0, 5); // Return HH:MM format
        }

        // If it's a decimal (Excel time format)
        if (is_numeric($timeValue)) {
            $hours = floor($timeValue * 24);
            $minutes = floor(($timeValue * 24 - $hours) * 60);
            return sprintf('%02d:%02d', $hours, $minutes);
        }

        return null;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'allow_late_sign_in' => 'nullable',
            'allow_early_sign_out' => 'nullable',
        ];
    }

    /**
     * Batch size for processing
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size for reading
     */
    public function chunkSize(): int
    {
        return 100;
    }
}
