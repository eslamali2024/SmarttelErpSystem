<?php

namespace Modules\Hr\Services\MasterData;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Hr\Models\PublicHoliday;
use Modules\Hr\Imports\PublicHolidayImport;

class PublicHolidayService
{
    /**
     * Store a newly created PublicHoliday in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\PublicHoliday
     */
    public function store(array $data): PublicHoliday
    {
        return PublicHoliday::create($data);
    }

    /**
     * Update the specified PublicHoliday in storage.
     *
     * @param  \Modules\Hr\Models\PublicHoliday  $publicHoliday
     * @param  array  $data
     * @return \Modules\Hr\Models\PublicHoliday
     */
    public function update(PublicHoliday $publicHoliday, array $data): PublicHoliday
    {
        $publicHoliday->update($data);

        return $publicHoliday;
    }

    /**
     * Remove the specified PublicHoliday from storage.
     *
     * @param  \Modules\Hr\Models\PublicHoliday  $publicHoliday
     * @return void
     */
    public function destroy(PublicHoliday $publicHoliday): void
    {
        $publicHoliday->delete();
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
        Excel::import(new PublicHolidayImport, 'imports/' . $fileName);
        unlink('imports/' . $fileName);
    }
}
