<?php

namespace App\Notifications\Approval;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewApprovalRequestNotification extends Notification
{
    use Queueable;

    private $item, $approvalRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct($item, $approvalRequest, public $user)
    {
        $this->item             =  $item;
        $this->approvalRequest  = $approvalRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'item_id'       => $this->item?->id,
            'request_id'    => $this->approvalRequest?->id,
            'employee_id'   => $this->item?->employee_id,
            'title'         => __('dashboard.title_new_approval_request', ['flow' => $this->approvalRequest?->approvalFlow?->name]),
            'message'       => __('dashboard.new_approval_request', ['employee' => $this->user?->name]),
            'route'         => route($this->approvalRequest?->approvalFlow?->redirect_route ?? 'dashboard', $this->item?->id ?? 'page'),
        ];
    }
}
