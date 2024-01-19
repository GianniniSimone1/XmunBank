<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    public function from(): BelongsTo
    {
        return $this->belongsTo(ContiCorrenti::class);
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(ContiCorrenti::class);
    }

    protected $fillable = [
        'to',
        'from',
        'value',
        'reason',
        'fee'
    ];
}
