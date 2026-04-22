<?php

namespace App\Support;

use App\Models\Company;
use App\Models\User;

class CurrentCompany
{
    public const SESSION_KEY = 'current_company_id';

    /**
     * Resolve the current company for the given (authenticated) user.
     * Falls back to the user's first company, creating a default one if necessary.
     */
    public static function resolve(User $user): Company
    {
        $sessionId = session(self::SESSION_KEY);

        if ($sessionId) {
            $company = $user->companies()->find($sessionId);
            if ($company) {
                return $company;
            }
        }

        $company = $user->ensureCompany();
        session([self::SESSION_KEY => $company->id]);
        return $company;
    }

    public static function set(Company $company): void
    {
        session([self::SESSION_KEY => $company->id]);
    }
}
