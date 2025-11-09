<?php

namespace App\Models\Approval;

use App\Traits\ScopeFilter;
use Illuminate\Database\Eloquent\Model;
use App\Models\Approval\ApprovalRequest;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprovalFlow extends Model
{
    use SoftDeletes, ScopeFilter;

    protected $fillable = [
        'name',
        'redirect_route',
        'approvable_type',
    ];

    /**
     * Retrieves all approval requests associated with this approval flow.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function approvalRequests()
    {
        return $this->hasMany(ApprovalRequest::class);
    }

    /**
     * Retrieves all approval flow steps associated with this approval flow.
     * The steps are ordered by their 'order' field.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function approvalFlowSteps()
    {
        return $this->hasMany(ApprovalFlowStep::class)->orderBy('order');
    }

    /**
     * Retrieves an instance of the model that this approval flow is associated with.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function approvable()
    {
        return new $this->approvable_type;
    }
}
