<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WorkItem extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'task_id',
        'title',
        'description',
        'status',
        'start_date',
        'due_date',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(WorkItemReport::class)->latest('report_date');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }

    public function progressPercent(): float
    {
        $targetGoal = $this->goals->firstWhere('metric_type', 'target');

        if (! $targetGoal || $targetGoal->target_value <= 0) {
            return 0;
        }

        return round(($targetGoal->current_value / $targetGoal->target_value) * 100, 1);
    }
}
