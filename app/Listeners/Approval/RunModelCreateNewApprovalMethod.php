<?php

namespace App\Listeners\Approval;

use App\Events\Approval\ApprovalCompleted;
use App\Events\Approval\CreateNewApproval;

class RunModelCreateNewApprovalMethod
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
    public function handle(CreateNewApproval $event)
    {
        if (method_exists($event->model, 'onCreateRequestApproval')) {
            $event->model->onCreateRequestApproval($event->request, $event->user);
        }
    }
}
