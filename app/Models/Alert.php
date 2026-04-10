<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['type', 'message', 'cost_centre_id', 'account_id', 'year', 'month', 'is_read', 'severity'])]
class Alert extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'message', 'cost_centre_id', 'account_id', 'year', 'month', 'is_read', 'severity'];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }
}
