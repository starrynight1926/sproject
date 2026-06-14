<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrgUnit extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'type',
        'sort_order',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(OrgUnit::class, 'parent_id')->orderBy('sort_order');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'org_unit_id');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'org_unit_id')->orderBy('sort_order');
    }
}
