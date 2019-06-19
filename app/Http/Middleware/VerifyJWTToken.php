<?php

namespace App\Http\Middleware;

use JWTAuth;
use JWTAuthException;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use App\Helpers\ErrorHelper;

class VerifyJWTToken
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
        try {
            if ($authorization = $request->header('API-KEY')) {
                $request->headers->set('Authorization', $authorization);
            }
            $customer =  JWTAuth::parseToken()->authenticate();
            if(!$customer) {
                return ErrorHelper::AUT_02();
            }
            $request->request->add(['jwt_customer_id' =>  $customer->customer_id]);
        } catch (JWTException $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return ErrorHelper::AUT_03();
            } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return ErrorHelper::AUT_02();
            } else {
                return ErrorHelper::AUT_01();
            }
        }
        return $next($request);
    }
}
