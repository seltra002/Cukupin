<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id', 'name', 'opening_balance', 'current_balance',
        'is_cash_flow', 'allow_negative', 'note',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_cash_flow' => 'boolean',
        'allow_negative' => 'boolean',
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function mutations(): HasMany
    {
        return $this->hasMany(WalletMutation::class);
    }

    /**
     * Terapkan mutasi ke saldo wallet ini, dengan validasi allow_negative.
     * Return false kalau mutasi ditolak (saldo bakal minus tapi allow_negative = false).
     */
    public function applyMutation(string $type, float $amount): bool
    {
        $delta = $type === 'in' ? $amount : -$amount;
        $newBalance = (float) $this->current_balance + $delta;

        if (! $this->allow_negative && $newBalance < 0) {
            return false;
        }

        $this->current_balance = $newBalance;
        $this->save();

        return true;
    }
}
