<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPostSkill extends Model
{
    protected $fillable = [
        'job_post_id',
        'skill_name',
        'weight',
        'min_proficiency',
        'order',
    ];

    protected $casts = [
        'weight' => 'integer',
        'min_proficiency' => 'integer',
        'order' => 'integer',
    ];

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }
}
