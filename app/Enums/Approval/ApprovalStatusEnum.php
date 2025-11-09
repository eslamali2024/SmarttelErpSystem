<?php

namespace App\Enums\Approval;

enum ApprovalStatusEnum: int
{
    case PENDING        = 1;
    case APPROVED       = 2;
    case REJECTED       = 3;
    case IN_PROGRESS    = 4;

    public function label(): string
    {
        return match ($this) {
            self::PENDING               => __('dashboard.pending'),
            self::APPROVED              => __('dashboard.approved'),
            self::REJECTED              => __('dashboard.rejected'),
            self::IN_PROGRESS           => __('dashboard.in_progress'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING        => 'text-warning',
            self::APPROVED       => 'text-success',
            self::REJECTED       => 'text-danger',
            self::IN_PROGRESS    => 'text-primary',
        };
    }

    public function bgColor(): string
    {
        return match ($this) {
            self::PENDING        => 'bg-warning',
            self::APPROVED       => 'bg-success',
            self::REJECTED       => 'bg-danger',
            self::IN_PROGRESS    => 'bg-primary',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING        => 'ri ri-time-line',
            self::APPROVED       => 'ri ri-checkbox-circle-line',
            self::REJECTED       => 'ri ri-close-circle-line',
            self::IN_PROGRESS    => 'ri ri-play-circle-line',
        };
    }
}
