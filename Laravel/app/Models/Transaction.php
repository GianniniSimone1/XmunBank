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
        return $this->belongsTo(ContiCorrenti::class, 'from', 'id');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(ContiCorrenti::class, 'to', 'id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class, 'type', 'id');
    }

    public function getFromAttribute()
    {
        return $this->from()->select(['id', 'owner_name', 'iban'])->first();
    }

    public function getToAttribute()
    {
        return $this->to()->select(['id', 'owner_name', 'iban'])->first();
    }

    public function getTypeAttribute(): string
    {
       return $this->type()->type;
    }


    protected $fillable = [
        'to',
        'from',
        'value',
        'reason',
        'fee',
        'type'
    ];
}
