<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContiCorrenti extends Model
{
    use HasFactory;

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner');
    }

    public function transactionsFrom(): HasMany
    {
        return $this->hasMany(Transaction::class, 'from', 'id');
    }

    public function transactionsTo(): HasMany
    {
        return $this->hasMany(Transaction::class, 'to', 'id');
    }

    public function joints(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'joints');
    }

    protected $fillable = [
        'owner'
        ];

}
