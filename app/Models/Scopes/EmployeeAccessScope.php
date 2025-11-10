<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class EmployeeAccessScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     *
     * @SuppressWarnings("unused")
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if (!$user) {
            return;
        }

        if ($user->hasAnyRole(['super-admin', 'general-manager', 'hr-manager', 'hr-staff'])) {
            return;
        }

        $builder->where(function ($query) use ($user) {
            $query->where('employee_id', $user->employee?->id)
                ->orWhereHas('employee.currentPosition.division', fn($q) => $q->where('manager_id', $user->id))
                ->orWhereHas('employee.currentPosition.department', fn($q) => $q->where('manager_id', $user->id))
                ->orWhereHas('employee.currentPosition.section', fn($q) => $q->where('manager_id', $user->id));
        });
    }
}
