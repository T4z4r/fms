<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['type', 'year', 'data'])]
class ReportCache extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'year' => 'integer',
        ];
    }
}
