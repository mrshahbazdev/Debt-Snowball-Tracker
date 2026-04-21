<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'monthly_revenue',
        'debt_allocation_percent',
        'minimum_cash_buffer',
        'new_debt_allowed',
        'currency',
    ];

    protected $casts = [
        'monthly_revenue' => 'decimal:2',
        'debt_allocation_percent' => 'decimal:3',
        'minimum_cash_buffer' => 'decimal:2',
        'new_debt_allowed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function allocationFraction(): float
    {
        return (float) $this->debt_allocation_percent / 100.0;
    }

    public function monthlyAllocation(): float
    {
        return round((float) $this->monthly_revenue * $this->allocationFraction(), 2);
    }
}
