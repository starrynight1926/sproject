<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeGoal extends Model
{
    protected $fillable = [
        'user_id',
        'position_id',
        'name',
        'type',
        'unit',
        'target_value',
        'current_value',
        'period',
    ];

    protected function casts(): array
    {
        return [
            'target_value' => 'decimal:2',
            'current_value' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function progressPercent(): float
    {
        if ($this->target_value <= 0) {
            return 0;
        }

        return min(100, round(($this->current_value / $this->target_value) * 100, 1));
    }
}
