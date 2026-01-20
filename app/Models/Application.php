<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'job_post_id',
        'jobseeker_id',
        'current_status',
        'applied_at',
        'cover_letter',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }

    public function jobseeker()
    {
        return $this->belongsTo(Jobseeker::class);
    }

    public function statuses()
    {
        return $this->hasMany(ApplicationStatus::class);
    }

    public function notes()
    {
        return $this->hasMany(EmployerNote::class);
    }
}
