<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionType extends Model
{
    use HasFactory;

    protected $fillable = [
       'type'
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'type', 'id');
    }


}
