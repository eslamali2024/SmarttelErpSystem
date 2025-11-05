<?php

namespace Modules\Hr\Services\MasterData;

use Modules\Hr\Models\TimeManagement;

class TimeManagementService
{
    /**
     * Store a newly created TimeManagement in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\TimeManagement
     */
    public function store(array $data): TimeManagement
    {
        return TimeManagement::create($data);
    }

    /**
     * Update the specified TimeManagement in storage.
     *
     * @param  \Modules\Hr\Models\TimeManagement  $timeManagement
     * @param  array  $data
     * @return \Modules\Hr\Models\TimeManagement
     */
    public function update(TimeManagement $timeManagement, array $data): TimeManagement
    {
        $timeManagement->update($data);

        return $timeManagement;
    }

    /**
     * Remove the specified TimeManagement from storage.
     *
     * @param  \Modules\Hr\Models\TimeManagement  $timeManagement
     * @return void
     */
    public function destroy(TimeManagement $timeManagement): void
    {
        $timeManagement->delete();
    }
}
