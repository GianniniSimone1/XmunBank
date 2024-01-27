<?php

namespace App\Models;

use App\Http\Controllers\TransactionController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PhpParser\Node\Expr\Array_;

class ContiCorrenti extends Model
{
    use HasFactory;

    public function getIbanAttribute(): string
    {
        return env('APP_NAME') . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }


    public function getOwnerNameAttribute(): string
    {
        $o = $this->owner()->first();
        return $o->cognome . ' ' . $o->nome;
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner')->select(['nome', 'cognome']);
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
        return $this->belongsToMany(User::class, 'joints', 'conti_correntis_id', 'user_id');
    }

    public function getCointestatariAttribute()
    {
        return $this->joints()->select(['nome', 'cognome', 'email'])->get()->makeHidden(['pivot']);
    }

    public function isOwnerOrJoint(int $userId): bool
    {
        return $this->owner === $userId || $this->joints->contains('id', $userId);
    }

    public function getBalanceAttribute()
    {
        $transactionsFrom = $this->transactionsFrom()->sum('value');
        $transactionsFromFee = $this->transactionsFrom()->sum('fee');
        $transactionsTo = $this->transactionsTo()->sum('value');

        return round(0 + $transactionsTo - $transactionsFrom - $transactionsFromFee, 2);
    }

    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'owner'
        ];

}
