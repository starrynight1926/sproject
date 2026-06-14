<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    protected $fillable = [
        'parent_id',
        'org_unit_id',
        'name',
        'sort_order',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'parent_id');
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Position::class, 'parent_id')->orderBy('sort_order');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'position_id');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(EmployeeGoal::class, 'position_id');
    }
}
