<?php

namespace Modules\Hr\Imports;

use Illuminate\Support\Collection;
use Modules\Hr\Models\PublicHoliday;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PublicHolidayImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as  $row) {
            if (!empty($row) && isset($row['name']) && isset($row['start_date'])) {
                PublicHoliday::updateOrCreate(
                    [
                        'name'              => $row['name'] ?? null,
                        'start_date'        => $this->dateFormatter($row['start_date']),
                    ],
                    [
                        'name'              => $row['name'] ?? null,
                        'start_date'        => $this->dateFormatter($row['start_date']) ?? null,
                        'end_date'          => $this->dateFormatter($row['end_date']) ?? null,
                        'actual_start_date' => $this->dateFormatter($row['actual_start_date']) ?? null,
                        'actual_end_date'   => $this->dateFormatter($row['actual_end_date']) ?? null,
                        'days'              => $row['days'] ?? null,
                    ]
                );
            }
        }
    }

    private function dateFormatter($rowName)
    {
        return Date::excelToDateTimeObject($rowName)->format('Y-m-d');
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
