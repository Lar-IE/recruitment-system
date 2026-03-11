<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobMatch extends Model
{
    protected $fillable = [
        'job_post_id',
        'jobseeker_id',
        'rule_score',
        'ai_semantic_score',
        'final_score',
    ];

    protected $casts = [
        'rule_score' => 'float',
        'ai_semantic_score' => 'float',
        'final_score' => 'float',
    ];

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    public function jobseeker(): BelongsTo
    {
        return $this->belongsTo(Jobseeker::class);
    }
}
