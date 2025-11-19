<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'file_path',
        'external_url',
        'file_name',
        'file_size',
        'mime_type',
        'group_id',
        'uploaded_by',
        'visibility',
        'is_active',
        'categories',
        'download_count',
        'published_at',
    ];

    protected $appends = ['formatted_file_size'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'categories' => 'array',
            'published_at' => 'datetime',
            'file_size' => 'integer',
            'download_count' => 'integer',
        ];
    }

    /**
     * Relationships
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function progress()
    {
        return $this->hasMany(ResourceProgress::class);
    }

    // public function comments()
    // {
    //     return $this->morphMany(Comment::class, 'commentable');
    // }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeForGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    /**
     * Accessors
     */
    public function getFileUrlAttribute()
    {
        if ($this->type === 'link') {
            return $this->external_url;
        }

        if ($this->file_path) {
            return Storage::url($this->file_path);
        }

        return null;
    }

    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Methods
     */
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function isAccessibleBy(User $user)
    {
        // Public resources are accessible to all authenticated users
        if ($this->visibility === 'public') {
            return true;
        }

        // Group resources are accessible to group members
        if ($this->visibility === 'group') {
            return $user->groups()
                ->where('groups.id', $this->group_id)
                ->where('group_user.status', 'approved')
                ->exists();
        }

        return false;
    }
}
