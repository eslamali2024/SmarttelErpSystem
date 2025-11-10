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

    public static function items(): array
    {
        return [
            self::PENDING->value        => self::PENDING->label(),
            self::APPROVED->value       => self::APPROVED->label(),
            self::REJECTED->value       => self::REJECTED->label(),
            self::IN_PROGRESS->value    => self::IN_PROGRESS->label(),
        ];
    }
}
