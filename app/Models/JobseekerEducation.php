<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobseekerEducation extends Model
{
    protected $table = 'jobseeker_education';

    protected $fillable = [
        'jobseeker_id',
        'institution',
        'degree',
        'field_of_study',
        'start_date',
        'end_date',
        'description',
        'order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function jobseeker()
    {
        return $this->belongsTo(Jobseeker::class);
    }
}
