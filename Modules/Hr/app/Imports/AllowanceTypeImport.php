<?php

namespace Modules\Hr\Imports;

use Illuminate\Support\Collection;
use Modules\Hr\Models\AllowanceType;
use Modules\Hr\Enums\AllowancesTypeEnum;
use Modules\Hr\Enums\AllowancesTaxableEnum;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class AllowanceTypeImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as  $row) {
            if (!empty($row) && isset($row['name'])) {
                AllowanceType::updateOrCreate(
                    [
                        'name'              => $row['name'] ?? null,
                    ],
                    [
                        'name'              => $row['name'] ?? null,
                        'taxable'           => strtolower($row['taxable'] ?? '') == 'taxable' ? AllowancesTaxableEnum::TAXABLE : AllowancesTaxableEnum::NOT_TAXABLE,
                        'type'              => strtolower($row['type'] ?? '') == 'recurring' ? AllowancesTypeEnum::RECURRING : AllowancesTypeEnum::OFF_CYCLE,
                        'deleted_at'        => null
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
