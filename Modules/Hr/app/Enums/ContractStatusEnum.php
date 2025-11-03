<?php

namespace Modules\Hr\Enums;

enum ContractStatusEnum: int
{
    case ACTIVE = 1;
    case IN_ACTIVE = 2;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('hr.active'),
            self::IN_ACTIVE => __('hr.in_active'),
        };
    }

    public function items(): array
    {
        return [
            self::ACTIVE->value => self::ACTIVE->label(),
            self::IN_ACTIVE->value => self::IN_ACTIVE->label(),
        ];
    }
}
