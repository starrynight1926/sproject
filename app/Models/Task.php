<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Task extends Model implements HasMedia
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'project_id',
        'goal_id',
        'title',
        'description',
        'status',
        'priority',
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

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function workItems(): HasMany
    {
        return $this->hasMany(WorkItem::class)->orderBy('sort_order');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_assignments')
            ->withPivot('assigned_at');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }
}
