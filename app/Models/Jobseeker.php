<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jobseeker extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'phone',
        'address',
        'barangay',
        'city',
        'province',
        'region',
        'country',
        'birth_date',
        'gender',
        'educational_attainment',
        'bio',
        'education',
        'experience',
        'skills',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function digitalIds()
    {
        return $this->hasMany(DigitalId::class);
    }

    public function educations()
    {
        return $this->hasMany(JobseekerEducation::class)->orderBy('order');
    }

    public function workExperiences()
    {
        return $this->hasMany(JobseekerWorkExperience::class)->orderBy('order');
    }

    public function getFullNameAttribute()
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->middle_name ?? '') . ' ' . ($this->last_name ?? ''));
    }
}
