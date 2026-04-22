<?php

namespace App\Http\Middleware;

use App\Support\CurrentCompany;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class EnsureCurrentCompany
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user) {
            $company = CurrentCompany::resolve($user);
            View::share('currentCompany', $company);
            View::share('userCompanies', $user->companies()->get());
            app()->instance('current.company', $company);
        }

        return $next($request);
    }
}
