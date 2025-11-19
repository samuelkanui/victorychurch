<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'leader_id',
        'is_active',
        'max_members',
        'meeting_schedule',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['current_members_count'];

    public function getCurrentMembersCountAttribute()
    {
        return $this->approvedMembers()->count();
    }

    /**
     * Relationships
     */
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['status', 'role', 'joined_at', 'status_changed_at', 'status_changed_by'])
            ->withTimestamps();
    }

    public function approvedMembers()
    {
        return $this->belongsToMany(User::class)
            ->wherePivot('status', 'approved')
            ->withPivot(['status', 'role', 'joined_at', 'status_changed_at', 'status_changed_by'])
            ->withTimestamps();
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function prayerRequests()
    {
        return $this->hasManyThrough(PrayerRequest::class, User::class, 'id', 'user_id', 'id', 'id')
            ->whereHas('user.groups', function ($query) {
                $query->where('groups.id', $this->id)
                      ->where('group_user.status', 'approved');
            });
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
