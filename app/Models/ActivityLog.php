<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = ['household_id', 'user_id', 'action', 'description'];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(int $householdId, ?int $userId, string $action, ?string $description = null): self
    {
        return self::create([
            'household_id' => $householdId,
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
        ]);
    }
}
