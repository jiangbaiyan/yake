<?php

namespace App\Http\Middleware;

use App\Helper\ApiResponse;
use Closure;

class Admin
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = \Session::get('user');
        if (!$user->is_admin){
            return $this->responsePermissionDenied();
        }
        return $next($request);
    }
}
