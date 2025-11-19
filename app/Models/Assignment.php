<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'created_by',
        'title',
        'description',
        'type',
        'due_date',
        'max_points',
        'instructions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'is_active' => 'boolean',
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

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDue($query)
    {
        return $query->where('due_date', '<=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('due_date', '>', now());
    }
}
