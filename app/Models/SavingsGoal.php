<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingsGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id', 'wallet_id', 'name', 'target_amount', 'deadline', 'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'deadline' => 'date',
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(SavingsContribution::class);
    }

    public function getCollectedAmountAttribute(): float
    {
        return (float) $this->contributions()->sum('amount');
    }

    public function getProgressPercentAttribute(): float
    {
        if ((float) $this->target_amount <= 0) {
            return 0;
        }

        return min(100, round(($this->collected_amount / (float) $this->target_amount) * 100, 1));
    }
}
