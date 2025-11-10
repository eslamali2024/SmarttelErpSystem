<?php

namespace App\Listeners\Approval;

use App\Events\Approval\ApprovalCompleted;

class RunModelApprovalCompletedMethod
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
    public function handle(ApprovalCompleted $event)
    {
        if (method_exists($event->model, 'onCompletedApprove')) {
            $event->model->onCompletedApprove($event->request, $event->user);
        }
    }
}
