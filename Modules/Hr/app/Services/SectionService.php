<?php

namespace  Modules\Hr\Services;

use Modules\Hr\Models\Section;

class SectionService
{
    /**
     * Store a newly created Section in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\Section
     */
    public function store(array $data): Section
    {
        return Section::create($data);
    }

    /**
     * Update the specified Section in storage.
     *
     * @param  \Modules\Hr\Models\Section  $section
     * @param  array  $data
     * @return \Modules\Hr\Models\Section
     */
    public function update(Section $section, array $data): Section
    {
        $section->update($data);

        return $section;
    }

    /**
     * Remove the specified Section from storage.
     *
     * @param  \Modules\Hr\Models\Section  $section
     * @return bool
     */
    public function destroy(Section $section): bool
    {
        return $section->delete();
    }
}
