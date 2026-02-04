<?php

namespace App\Models;

use App\Enums\EmployerSubUserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class EmployerSubUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'employer_id',
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => EmployerSubUserRole::class,
        ];
    }

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
