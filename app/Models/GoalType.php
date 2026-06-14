<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoalType extends Model
{
    protected $fillable = [
        'key',
        'label',
        'sort_order',
    ];
}
