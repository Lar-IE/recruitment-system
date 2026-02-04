<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobseekerWorkExperience extends Model
{
    protected $table = 'jobseeker_work_experience';

    protected $fillable = [
        'jobseeker_id',
        'company',
        'position',
        'start_date',
        'end_date',
        'is_current',
        'description',
        'order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function jobseeker()
    {
        return $this->belongsTo(Jobseeker::class);
    }
}
