<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'company_email',
        'phone',
        'website',
        'industry',
        'company_size',
        'address',
        'status',
        'approved_at',
        'suspended_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'suspended_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobPosts()
    {
        return $this->hasMany(JobPost::class);
    }

    public function notes()
    {
        return $this->hasMany(EmployerNote::class);
    }

    public function subUsers()
    {
        return $this->hasMany(EmployerSubUser::class);
    }
}
