<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtPayment extends Model
{
    use HasFactory;

    protected $fillable = ['debt_id', 'amount', 'date', 'note'];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function debt(): BelongsTo
    {
        return $this->belongsTo(Debt::class);
    }
}
