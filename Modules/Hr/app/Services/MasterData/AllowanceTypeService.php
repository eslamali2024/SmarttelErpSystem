<?php

namespace Modules\Hr\Services\MasterData;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Hr\Models\AllowanceType;
use Modules\Hr\Imports\AllowanceTypeImport;

class AllowanceTypeService
{
    /**
     * Store a newly created AllowanceType in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\AllowanceType
     */
    public function store(array $data): AllowanceType
    {
        return AllowanceType::create($data);
    }

    /**
     * Update the specified AllowanceType in storage.
     *
     * @param  \Modules\Hr\Models\AllowanceType  $allowanceType
     * @param  array  $data
     * @return \Modules\Hr\Models\AllowanceType
     */
    public function update(AllowanceType $allowanceType, array $data): AllowanceType
    {
        $allowanceType->update($data);

        return $allowanceType;
    }

    /**
     * Remove the specified AllowanceType from storage.
     *
     * @param  \Modules\Hr\Models\AllowanceType  $allowanceType
     * @return void
     */
    public function destroy(AllowanceType $allowanceType): void
    {
        $allowanceType->delete();
    }

    /**
     * Import Allowance Types from an Excel file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return void
     */
    public function import($file): void
    {
        $fileName = Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move('imports', $fileName);
        Excel::import(new AllowanceTypeImport, 'imports/' . $fileName);
        unlink('imports/' . $fileName);
    }
}
