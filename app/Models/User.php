<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'capabilities',
        'profile_photo_path',
        'google_id',
        'last_login_at',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'capabilities' => 'array',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Role helper methods
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isLeader(): bool
    {
        return $this->role === 'leader';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    /**
     * Relationships
     */
    public function ledGroups()
    {
        return $this->hasMany(Group::class, 'leader_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class)
            ->withPivot(['status', 'role', 'joined_at', 'status_changed_at', 'status_changed_by'])
            ->withTimestamps();
    }

    public function prayerRequests()
    {
        return $this->hasMany(PrayerRequest::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'created_by');
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class, 'created_by');
    }

    public function meetingAttendances()
    {
        return $this->hasMany(MeetingAttendance::class);
    }

    public function uploadedResources()
    {
        return $this->hasMany(Resource::class, 'uploaded_by');
    }

    public function resourceProgress()
    {
        return $this->hasMany(ResourceProgress::class);
    }

    public function deletionRequest()
    {
        return $this->hasOne(DeletionRequest::class);
    }

    public function hasPendingDeletionRequest(): bool
    {
        return $this->deletionRequest()->where('status', 'pending')->exists();
    }

    public function hasApprovedDeletionRequest(): bool
    {
        return $this->deletionRequest()->where('status', 'approved')->exists();
    }
}
