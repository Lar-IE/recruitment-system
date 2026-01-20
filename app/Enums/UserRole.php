<?php

namespace App\Enums;

enum UserRole: string
{
    case Jobseeker = 'jobseeker';
    case Employer = 'employer';
    case Admin = 'admin';

    public static function labels(): array
    {
        return [
            self::Jobseeker->value => 'Jobseeker',
            self::Employer->value => 'Employer',
            self::Admin->value => 'Admin',
        ];
    }
}
