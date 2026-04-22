<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cashflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'period',
        'revenue',
        'debt_allocation',
        'available_cash',
        'notes',
    ];

    protected $casts = [
        'period' => 'date',
        'revenue' => 'decimal:2',
        'debt_allocation' => 'decimal:2',
        'available_cash' => 'decimal:2',
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

    public function paidAllocation(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function remainingAllocation(): float
    {
        return max(0.0, (float) $this->debt_allocation - $this->paidAllocation());
    }
}
