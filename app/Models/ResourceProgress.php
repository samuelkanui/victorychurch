<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceProgress extends Model
{
    use HasFactory;

    protected $table = 'resource_progress';

    protected $fillable = [
        'resource_id',
        'user_id',
        'status',
        'progress_percentage',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Methods
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'progress_percentage' => 100,
            'completed_at' => now(),
        ]);
    }

    public function updateProgress($percentage)
    {
        $this->update([
            'progress_percentage' => min(100, max(0, $percentage)),
            'started_at' => $this->started_at ?? now(),
        ]);

        if ($percentage >= 100) {
            $this->markAsCompleted();
        }
    }
}
