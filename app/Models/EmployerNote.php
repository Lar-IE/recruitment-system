<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployerNote extends Model
{
    protected $fillable = [
        'employer_id',
        'application_id',
        'note',
        'created_by',
    ];

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
