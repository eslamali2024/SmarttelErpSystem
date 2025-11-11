<?php

namespace Modules\Hr\Services\MasterData;

use Illuminate\Support\Str;
use Modules\Hr\Models\LeaveType;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Hr\Imports\LeaveTypeImport;

class LeaveTypeService
{
    /**
     * Store a newly created LeaveType in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\LeaveType
     */
    public function store(array $data): LeaveType
    {
        return LeaveType::create($data);
    }

    /**
     * Update the specified LeaveType in storage.
     *
     * @param  \Modules\Hr\Models\LeaveType  $leaveType
     * @param  array  $data
     * @return \Modules\Hr\Models\LeaveType
     */
    public function update(LeaveType $leaveType, array $data): LeaveType
    {
        $leaveType->update($data);

        return $leaveType;
    }

    /**
     * Remove the specified LeaveType from storage.
     *
     * @param  \Modules\Hr\Models\LeaveType  $leaveType
     * @return void
     */
    public function destroy(LeaveType $leaveType): void
    {
        $leaveType->delete();
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
        Excel::import(new LeaveTypeImport, 'imports/' . $fileName);
        unlink('imports/' . $fileName);
    }
}
