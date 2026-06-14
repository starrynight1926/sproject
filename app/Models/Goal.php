<?php

namespace App\Models;

use Database\Factories\GoalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    /** @use HasFactory<GoalFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'metric_type',
        'phase_id',
        'task_id',
        'work_item_id',
        'name',
        'type',
        'unit',
        'target_value',
        'current_value',
    ];

    protected function casts(): array
    {
        return [
            'target_value' => 'decimal:2',
            'current_value' => 'decimal:2',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function workItem(): BelongsTo
    {
        return $this->belongsTo(WorkItem::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function progressPercent(): float
    {
        if ($this->target_value <= 0) {
            return 0;
        }

        return min(100, round(($this->current_value / $this->target_value) * 100, 1));
    }
}
