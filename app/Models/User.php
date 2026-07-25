<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'household_id', 'role', 'permission',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function ownedHousehold(): HasOne
    {
        return $this->hasOne(Household::class, 'owner_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function telegramLink(): HasOne
    {
        return $this->hasOne(TelegramLink::class);
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function canInput(): bool
    {
        return $this->isOwner() || $this->permission === 'can_input';
    }
}
