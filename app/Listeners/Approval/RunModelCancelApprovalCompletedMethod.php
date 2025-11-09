<?php

namespace App\Listeners\Approval;

use App\Events\Approval\CancelApprovalCompleted;

class RunModelCancelApprovalCompletedMethod
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CancelApprovalCompleted $event)
    {
        if (method_exists($event->model, 'onCancelCompletedApprove')) {
            $event->model->onCancelCompletedApprove($event->request, $event->user);
        }
    }
}
