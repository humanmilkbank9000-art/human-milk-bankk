<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Session::has('account_id')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $currentRole = Session::get('account_role');
        
        if ($currentRole !== $role) {
            $message = $role === 'admin' 
                ? 'You are currently logged in as a User. Please logout and login with an Admin account to access this page.'
                : 'You are currently logged in as an Admin. Please logout and login with a User account to access this page.';
            
            return redirect()->route('login')->with('info', $message);
        }

        return $next($request);
    }
}
