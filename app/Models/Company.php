<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'industry',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setting(): HasOne
    {
        return $this->hasOne(Setting::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }

    public function cashflows(): HasMany
    {
        return $this->hasMany(Cashflow::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getOrCreateSetting(): Setting
    {
        return $this->setting()->firstOrCreate(
            [],
            [
                'user_id'                 => $this->user_id,
                'monthly_revenue'         => 0,
                'debt_allocation_percent' => 1.000,
                'minimum_cash_buffer'     => 0,
                'new_debt_allowed'        => false,
                'currency'                => 'EUR',
            ]
        );
    }
}
