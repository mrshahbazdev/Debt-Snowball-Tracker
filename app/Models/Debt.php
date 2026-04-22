<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Debt extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_PAID = 'PAID';

    protected $fillable = [
        'user_id',
        'company_id',
        'creditor',
        'original_balance',
        'current_balance',
        'minimum_payment',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'original_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'minimum_payment' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function progressPercent(): float
    {
        if ((float) $this->original_balance <= 0) {
            return 0.0;
        }
        $paid = (float) $this->original_balance - (float) $this->current_balance;
        return round(max(0, min(100, ($paid / (float) $this->original_balance) * 100)), 1);
    }
}
