<?php

namespace App\Http\Middleware;

use App\Helper\ApiResponse;
use App\Model\UserModel;
use Closure;

class Admin
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \App\Exceptions\UnAuthorizedException
     */
    public function handle($request, Closure $next)
    {
        $user = UserModel::getCurUser();
        if (!$user->is_admin){
            return $this->responsePermissionDenied();
        }
        return $next($request);
    }
}
