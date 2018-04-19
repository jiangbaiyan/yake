<?php

namespace App\Http\Middleware;

use App\Helper\ApiResponse;
use Closure;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JWTMiddleware extends BaseMiddleware
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
            $this->checkForToken($request);
            if (!JWTAuth::parseToken()->authenticate()){
                return $this->responseUnauthorized();
            };
        }catch (UnauthorizedHttpException $e){
            return $this->responseUnauthorized($e->getMessage());
        } catch (\Exception $e){
            return $this->responseUnauthorized($e->getMessage());
        }
        return $next($request);
    }
}
