<?php

namespace App\Enums\Approval;

enum ApprovalTypeEnum: int
{
    case USER                   = 1;
    case ROLE                   = 2;
    case PERMISSIONS            = 3;
    case SECTOR                 = 4;
    case DIVISION               = 5;
    case DEPARTMENT             = 6;
    case SECTION                = 7;
    case DEPARTMENT_REQUEST     = 8;
    case DEPARTMENT_APPROVAL    = 9;


    public function label(): string
    {
        return match ($this) {
            self::USER                   => 'User',
            self::ROLE                   => 'Role',
            self::PERMISSIONS            => 'Permissions',
            self::SECTOR                 => 'Sector',
            self::DIVISION               => 'Division',
            self::DEPARTMENT             => 'Department',
            self::SECTION                => 'Section',
            self::DEPARTMENT_REQUEST     => 'Department Request',
            self::DEPARTMENT_APPROVAL    => 'Department Approval',
        };
    }

    public static function items(): array
    {
        return [
            self::USER->value                   => self::USER->label(),
            self::ROLE->value                   => self::ROLE->label(),
            self::PERMISSIONS->value            => self::PERMISSIONS->label(),
            self::SECTOR->value                 => self::SECTOR->label(),
            self::DIVISION->value               => self::DIVISION->label(),
            self::DEPARTMENT->value             => self::DEPARTMENT->label(),
            self::SECTION->value                => self::SECTION->label(),
            self::DEPARTMENT_REQUEST->value     => self::DEPARTMENT_REQUEST->label(),
            self::DEPARTMENT_APPROVAL->value    => self::DEPARTMENT_APPROVAL->label(),
        ];
    }
}
