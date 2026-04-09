<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['actual_id', 'description', 'transaction_date'])]
class ActualDetail extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
        ];
    }

    public function actual(): BelongsTo
    {
        return $this->belongsTo(Actual::class);
    }
}
