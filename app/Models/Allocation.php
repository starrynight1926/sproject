<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Allocation extends Model
{
    protected $fillable = [
        'project_id',
        'parent_id',
        'org_unit_id',
        'position_id',
        'user_id',
        'target_value',
        'staff_count',
        'budget_amount',
        'note',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Allocation::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Allocation::class, 'parent_id');
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBudgetPercentAttribute(): float
    {
        $totalBudget = $this->project?->budget ?? 0;

        if ($totalBudget <= 0) {
            return 0;
        }

        return round(($this->budget_amount / $totalBudget) * 100, 2);
    }
}
