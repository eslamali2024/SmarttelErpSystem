<?php

namespace Modules\Hr\Services\Organization;

use Modules\Hr\Models\Position;

class PositionService
{
    /**
     * Store a newly created Position in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\Position
     */
    public function store(array $data): Position
    {
        return Position::create($data);
    }

    /**
     * Update the specified Position in storage.
     *
     * @param  \Modules\Hr\Models\Position  $position
     * @param  array  $data
     * @return \Modules\Hr\Models\Position
     */
    public function update(Position $position, array $data): Position
    {
        $position->update($data);

        return $position;
    }

    /**
     * Remove the specified Position from storage.
     *
     * @param  \Modules\Hr\Models\Position  $position
     * @return void
     */
    public function destroy(Position $position): void
    {
        $position->delete();
    }
}
