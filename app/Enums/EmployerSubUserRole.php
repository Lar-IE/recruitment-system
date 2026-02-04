<?php

namespace App\Enums;

enum EmployerSubUserRole: string
{
    case Admin = 'admin';
    case Recruiter = 'recruiter';
    case Viewer = 'viewer';

    public static function labels(): array
    {
        return [
            self::Admin->value => 'Admin',
            self::Recruiter->value => 'Recruiter',
            self::Viewer->value => 'Viewer',
        ];
    }

    public static function values(): array
    {
        return array_map(
            static fn (self $role) => $role->value,
            self::cases()
        );
    }
}
