<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DigitalId extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'jobseeker_id',
        'employer_id',
        'job_post_id',
        'file_path',
        'photo_path',
        'company_name',
        'job_title',
        'employee_identifier',
        'issue_date',
        'status',
        'issued_by',
        'public_token',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    public function jobseeker()
    {
        return $this->belongsTo(Jobseeker::class);
    }

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
