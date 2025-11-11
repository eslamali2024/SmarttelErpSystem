<?php

namespace Modules\Hr\Imports;

use Modules\Hr\Models\LeaveType;
use Illuminate\Support\Collection;
use Modules\Hr\Enums\LeaveTypeUnitEnum;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class LeaveTypeImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if (isset($row['name'])) {
                LeaveType::create([
                    'name'                          => $row['name'],
                    'unit'                          => (isset($row['unit']) && strtolower($row['unit']) == 'hours') ? LeaveTypeUnitEnum::HOURS->value : LeaveTypeUnitEnum::DAYS->value,
                    'company_amount'                => (isset($row['company_amount']) && is_numeric($row['company_amount'])) ? $row['company_amount'] : 0,
                    'for_employee'                  => (isset($row['for_employee']) && strtolower($row['for_employee']) == 'yes') ? true : false,
                    'duration_deducted_percentage'  => (isset($row['duration_deducted_percentage']) && is_numeric($row['duration_deducted_percentage'])) ? $row['duration_deducted_percentage'] : 0,
                    'salary_deducted_percentage'    => (isset($row['salary_deducted_percentage']) && is_numeric($row['salary_deducted_percentage'])) ? $row['salary_deducted_percentage'] : 0,
                ]);
            }
        }
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
