<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Customer = 'customer';
    case Staff = 'staff';

    public function redirectRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.dashboard',
            self::Customer => 'customer.home',
            self::Staff => 'staff.dashboard',
        };
    }
}
