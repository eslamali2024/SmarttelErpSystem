<?php

namespace Modules\Hr\Http\Controllers\MasterData;

use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use Modules\Hr\Models\PublicHoliday;
use App\Http\Controllers\TransactionController;
use App\Http\Requests\ImportFileRequest;
use Modules\Hr\Services\MasterData\PublicHolidayService;
use Modules\Hr\Http\Requests\MasterData\PublicHolidayRequest;

class PublicHolidayController extends TransactionController
{
    private $path = 'Hr::MasterData/PublicHolidays/';

    public function __construct(private PublicHolidayService $publicHolidayService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('public_holiday_access'), 403);

        return Inertia::render($this->path . 'PublicHolidaysListComponent', [
            'public_holidays'    => PublicHoliday::filter(request()->query() ?? [])->paginate(request('perPage', 10)),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PublicHolidayRequest $request)
    {
        abort_if(Gate::denies('public_holiday_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->publicHolidayService->store($request->validated());
            return redirect()->route('hr.master-data.public-holidays.index');
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PublicHolidayRequest $request, PublicHoliday $publicHoliday)
    {
        abort_if(Gate::denies('public_holiday_edit'), 403);

        return $this->withTransaction(function () use ($request, $publicHoliday) {
            $this->publicHolidayService->update($publicHoliday, $request->validated());
            return redirect()->route('hr.master-data.public-holidays.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PublicHoliday $publicHoliday)
    {
        abort_if(Gate::denies('public_holiday_delete'), 403);

        return $this->withTransaction(function () use ($publicHoliday) {
            $this->publicHolidayService->destroy($publicHoliday);
            return redirect()->route('hr.master-data.public-holidays.index');
        });
    }

    /**
     * Import Public Holidays from an Excel file.
     *
     * @param ImportFileRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(ImportFileRequest $request)
    {
        return $this->withTransaction(function () use ($request) {
            $this->publicHolidayService->import($request->file('import_file'));
            return redirect()->route('hr.master-data.public-holidays.index');
        });
    }

    /**
     * Download a public holiday template in Excel format.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate()
    {
        return response()->download(public_path('assets/imports/public_holiday_template.xlsx'));
    }
}
