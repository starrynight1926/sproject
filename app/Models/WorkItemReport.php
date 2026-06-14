<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WorkItemReport extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'work_item_id',
        'user_id',
        'report_date',
        'progress_value',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'progress_value' => 'decimal:2',
        ];
    }

    public function workItem(): BelongsTo
    {
        return $this->belongsTo(WorkItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }
}
