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

    public function getFromDetailsAttribute()
    {
        $o = $this->from()->first();
        return ['id' => $o->id, 'iban' => $o->iban, 'owner_name' => $o->owner_name];
    }

    public function getToDetailsAttribute()
    {
        $o = $this->to()->first();
        return ['id' => $o->id, 'iban' => $o->iban, 'owner_name' => $o->owner_name];
    }

    public function getTypeDetailsAttribute(): string
    {
       return $this->type()->first()->type;
    }


    protected $fillable = [
        'to',
        'from',
        'value',
        'reason',
        'fee',
        'type'
    ];

    protected $casts = [
        'created_at' => 'datetime',
         'fee' => 'decimal:2',
        'value' => 'decimal:2',
    ];
}
