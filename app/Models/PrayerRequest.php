<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrayerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'privacy',
        'is_anonymous',
        'is_urgent',
        'answered_at',
        'leader_response',
        'responded_at',
        'responded_by',
        'moderation_note',
        'moderated_at',
        'moderated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'is_urgent' => 'boolean',
            'answered_at' => 'datetime',
            'responded_at' => 'datetime',
            'moderated_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function prayers()
    {
        return $this->belongsToMany(User::class, 'prayer_request_user')
            ->withPivot('prayed_at')
            ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopePublic($query)
    {
        return $query->where('privacy', 'public');
    }

    public function scopeGroup($query)
    {
        return $query->where('privacy', 'group');
    }

    public function scopePrivate($query)
    {
        return $query->where('privacy', 'private');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAnswered($query)
    {
        return $query->where('status', 'answered');
    }

    public function scopeFlagged($query)
    {
        return $query->where('status', 'flagged');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }
}
