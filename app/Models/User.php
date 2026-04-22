<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class)->orderBy('name');
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

    /**
     * Ensures this user has at least one company and returns their primary one.
     */
    public function ensureCompany(): Company
    {
        $company = $this->companies()->first();
        if (!$company) {
            $company = $this->companies()->create(['name' => 'My Company']);
        }
        return $company;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
