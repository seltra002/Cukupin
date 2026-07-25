<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id', 'type', 'party_name', 'amount', 'date', 'due_date', 'status', 'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'due_date' => 'date',
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->amount - $this->paid_amount);
    }

    public function refreshStatus(): void
    {
        $remaining = $this->remaining_amount;

        if ($remaining <= 0) {
            $this->status = 'paid';
        } elseif ($remaining < $this->amount) {
            $this->status = 'partial';
        } else {
            $this->status = 'unpaid';
        }

        $this->save();
    }
}
