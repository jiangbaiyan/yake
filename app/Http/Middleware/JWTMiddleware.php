<?php

namespace App\Http\Middleware;

use App\Helper\ApiResponse;
use Closure;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
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
        //JWT验证
        try{
            if (!$user = JWTAuth::parseToken()->authenticate()){
                return $this->responseUnauthorized();
            };
        }catch (\Exception $e){
            return $this->responseUnauthorized($e->getMessage());
        }
        //通过认证，存入session
        Session::put('user',$user);
        return $next($request);
    }
}
