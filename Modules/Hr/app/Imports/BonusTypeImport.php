<?php

namespace Modules\Hr\Imports;

use Modules\Hr\Models\BonusType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class BonusTypeImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as  $row) {
            if (!empty($row) && isset($row['name'])) {
                BonusType::updateOrCreate(
                    [
                        'name'              => $row['name'] ?? null,
                    ],
                    [
                        'name'              => $row['name'] ?? null,
                    ]
                );
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
