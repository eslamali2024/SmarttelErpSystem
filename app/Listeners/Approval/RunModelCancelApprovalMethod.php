<?php

namespace App\Listeners\Approval;

use App\Events\Approval\CancelApproval;

class RunModelCancelApprovalMethod
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
    public function handle(CancelApproval $event)
    {
        if (method_exists($event->model, 'onCancelApprove')) {
            $event->model->onCancelApprove($event->request, $event->user);
        }
    }
}
