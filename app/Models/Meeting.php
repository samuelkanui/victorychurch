<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'created_by',
        'title',
        'description',
        'type',
        'meeting_type',
        'scheduled_at',
        'duration_minutes',
        'location',
        'meeting_url',
        'max_attendees',
        'status',
        'is_recurring',
        'recurrence_pattern',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'is_recurring' => 'boolean',
        ];
    }

    /**
     * Relationships
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendees()
    {
        return $this->hasMany(MeetingAttendance::class);
    }

    public function attendances()
    {
        return $this->hasMany(MeetingAttendance::class);
    }

    /**
     * Scopes
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
                    ->where('status', 'scheduled');
    }

    public function scopePast($query)
    {
        return $query->where('scheduled_at', '<', now());
    }
}
