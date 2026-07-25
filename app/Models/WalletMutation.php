<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id', 'target_wallet_id', 'user_id', 'type', 'amount', 'date', 'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function targetWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'target_wallet_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
