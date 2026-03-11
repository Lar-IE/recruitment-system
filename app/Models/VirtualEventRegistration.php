<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualEventRegistration extends Model
{
    protected $fillable = [
        'virtual_event_id',
        'jobseeker_id',
        'registered_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    public function virtualEvent()
    {
        return $this->belongsTo(VirtualEvent::class);
    }

    public function jobseeker()
    {
        return $this->belongsTo(Jobseeker::class);
    }
}
