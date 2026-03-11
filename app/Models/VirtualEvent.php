<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VirtualEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'date',
        'start_time',
        'end_time',
        'platform',
        'meeting_link',
        'registration_deadline',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'registration_deadline' => 'datetime',
    ];

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }

    public function registrations()
    {
        return $this->hasMany(VirtualEventRegistration::class);
    }

    public function registeredJobseekers()
    {
        return $this->belongsToMany(Jobseeker::class, 'virtual_event_registrations')
            ->withPivot('registered_at')
            ->withTimestamps();
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming';
    }

    public function isOngoing(): bool
    {
        if ($this->status === 'ongoing') {
            return true;
        }

        // Auto-check: if status is upcoming but current time is between start and end, consider it ongoing
        if ($this->status === 'upcoming') {
            $eventStart = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->start_time);
            $eventEnd = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->end_time);
            $now = now();

            if ($now->between($eventStart, $eventEnd)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the meeting link should be shown to registered jobseekers.
     * Available from 1 hour before event start until event end.
     * Uses the application timezone (config app.timezone) so event times
     * match the user's local time when APP_TIMEZONE is set correctly in .env.
     */
    public function isMeetingLinkAvailable(): bool
    {
        if ($this->isCancelled() || $this->isCompleted()) {
            return false;
        }

        $tz = config('app.timezone', 'UTC');
        $dateStr = $this->date->format('Y-m-d');
        $startStr = trim((string) $this->start_time);
        $endStr = trim((string) $this->end_time);

        $eventStart = \Carbon\Carbon::parse($dateStr . ' ' . $startStr, $tz);
        $eventEnd = \Carbon\Carbon::parse($dateStr . ' ' . $endStr, $tz);
        $oneHourBefore = $eventStart->copy()->subHour();
        $now = now($tz);

        return $now->between($oneHourBefore, $eventEnd);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canRegister(?Jobseeker $jobseeker = null): bool
    {
        if (!$jobseeker) {
            return false;
        }

        if ($this->isCancelled() || $this->isCompleted()) {
            return false;
        }

        if ($this->registration_deadline && now()->isAfter($this->registration_deadline)) {
            return false;
        }

        // Check if already registered
        if ($this->registrations()->where('jobseeker_id', $jobseeker->id)->exists()) {
            return false;
        }

        return true;
    }

    public function getEventDateTimeAttribute()
    {
        return $this->date->format('Y-m-d') . ' ' . $this->start_time;
    }
}
