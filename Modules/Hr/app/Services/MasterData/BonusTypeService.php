<?php

namespace Modules\Hr\Services\MasterData;

use Illuminate\Support\Str;
use Modules\Hr\Models\BonusType;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Hr\Imports\BonusTypeImport;

class BonusTypeService
{
    /**
     * Store a newly created BonusType in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\BonusType
     */
    public function store(array $data): BonusType
    {
        return BonusType::create($data);
    }

    /**
     * Update the specified BonusType in storage.
     *
     * @param  \Modules\Hr\Models\BonusType  $bonusType
     * @param  array  $data
     * @return \Modules\Hr\Models\BonusType
     */
    public function update(BonusType $bonusType, array $data): BonusType
    {
        $bonusType->update($data);

        return $bonusType;
    }

    /**
     * Remove the specified BonusType from storage.
     *
     * @param  \Modules\Hr\Models\BonusType  $bonusType
     * @return void
     */
    public function destroy(BonusType $bonusType): void
    {
        $bonusType->delete();
    }

    /**
     * Import Public Holidays from an Excel file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return void
     */
    public function import($file): void
    {
        $fileName = Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move('imports', $fileName);
        Excel::import(new BonusTypeImport, 'imports/' . $fileName);
        unlink('imports/' . $fileName);
    }
}
