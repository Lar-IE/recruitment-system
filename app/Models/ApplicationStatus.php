<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationStatus extends Model
{
    protected $fillable = [
        'application_id',
        'status',
        'note',
        'interview_at',
        'interview_link',
        'set_by',
    ];

    protected $casts = [
        'interview_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function setBy()
    {
        return $this->belongsTo(User::class, 'set_by');
    }
}
