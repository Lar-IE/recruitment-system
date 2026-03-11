<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employer_id',
        'title',
        'slug',
        'location',
        'job_type',
        'description',
        'responsibilities',
        'benefits',
        'requirements',
        'salary_min',
        'salary_max',
        'currency',
        'salary_type',
        'salary_daily',
        'salary_monthly',
        'status',
        'application_deadline',
        'published_at',
        'closed_at',
    ];

    protected $casts = [
        'application_deadline' => 'date',
        'published_at' => 'datetime',
        'closed_at' => 'datetime',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'salary_daily' => 'decimal:2',
        'salary_monthly' => 'decimal:2',
    ];

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function requiredSkills()
    {
        return $this->hasMany(JobPostSkill::class)->orderBy('order');
    }

    public function jobMatches(): HasMany
    {
        return $this->hasMany(JobMatch::class)->orderByDesc('final_score');
    }
}
