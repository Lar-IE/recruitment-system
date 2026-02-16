<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobseekerSkill extends Model
{
    protected $fillable = [
        'jobseeker_id',
        'skill_name',
        'proficiency_percentage',
        'order',
    ];

    protected $casts = [
        'proficiency_percentage' => 'integer',
        'order' => 'integer',
    ];

    public function jobseeker(): BelongsTo
    {
        return $this->belongsTo(Jobseeker::class);
    }
}
