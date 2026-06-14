<?php

namespace App\Models;

use Database\Factories\ExcelFormulaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelFormula extends Model
{
    /** @use HasFactory<ExcelFormulaFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'syntax',
        'category',
        'description',
        'note',
        'example',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
        ];
    }
}
