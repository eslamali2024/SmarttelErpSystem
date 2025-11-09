<?php

namespace App\Listeners\Approval;

use App\Events\Approval\FirstApprovalCompleted;

class RunModelFirstApprovalCompletedMethod
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
    public function handle(FirstApprovalCompleted $event)
    {
        if (method_exists($event->model, 'onCompletedFirstApprove')) {
            $event->model->onCompletedFirstApprove($event->request, $event->user);
        }
    }
}
