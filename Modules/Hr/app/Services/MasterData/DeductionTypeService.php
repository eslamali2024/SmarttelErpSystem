<?php

namespace Modules\Hr\Services\MasterData;

use Illuminate\Support\Str;
use Modules\Hr\Models\DeductionType;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Hr\Imports\DeductionTypeImport;

class DeductionTypeService
{
    /**
     * Store a newly created DeductionType in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\DeductionType
     */
    public function store(array $data): DeductionType
    {
        return DeductionType::create($data);
    }

    /**
     * Update the specified DeductionType in storage.
     *
     * @param  \Modules\Hr\Models\DeductionType  $deductionType
     * @param  array  $data
     * @return \Modules\Hr\Models\DeductionType
     */
    public function update(DeductionType $deductionType, array $data): DeductionType
    {
        $deductionType->update($data);

        return $deductionType;
    }

    /**
     * Remove the specified DeductionType from storage.
     *
     * @param  \Modules\Hr\Models\DeductionType  $deductionType
     * @return void
     */
    public function destroy(DeductionType $deductionType): void
    {
        $deductionType->delete();
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
        Excel::import(new DeductionTypeImport, 'imports/' . $fileName);
        unlink('imports/' . $fileName);
    }
}
