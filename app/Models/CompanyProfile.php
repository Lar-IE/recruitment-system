<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $fillable = [
        'employer_id',
        'company_name',
        'description',
        'industry',
        'company_size',
        'year_established',
        'website',
        'contact_email',
        'contact_number',
        'address',
        'logo',
        'cover_photo',
    ];

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }
}
