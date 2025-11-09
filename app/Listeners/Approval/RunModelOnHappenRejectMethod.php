<?php

namespace App\Listeners\Approval;

use App\Events\Approval\RejectApproval;

class RunModelOnHappenRejectMethod
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
    public function handle(RejectApproval $event)
    {
        if (method_exists($event->model, 'onHappenReject')) {
            $event->model->onHappenReject($event->request, $event->user);
        }
    }
}
