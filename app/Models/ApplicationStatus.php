<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationStatus extends Model
{
    protected $fillable = [
        'application_id',
        'status',
        'note',
        'set_by',
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
