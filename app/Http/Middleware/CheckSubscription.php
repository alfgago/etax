<?php

namespace App\Http\Middleware;

use Closure;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
        $company = currentCompanyModel();
        if ($company->subscription->status == 3) {
            return redirect('/login');
        }
        return $next($request);
    }
    
}
