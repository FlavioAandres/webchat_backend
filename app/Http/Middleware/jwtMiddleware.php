<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;

class jwtMiddleware
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
        try{
            $user = JWTAuth::parseToken()->authenticate();
        }catch(Exception $e){
            if($e instanceof \Tymon\JWTAuth\Exeptions\TokenInvalidExeption){
                return response()->json(['error'=>'Token sent is invalid']);
            }elseif($e instanceof \Tymon\JWTAuth\Exeptions\TokenExpiredException){
                return response()->json(['error'=>'Token is  expired']);
            }else{
                return response()->json(['error'=>'Something is wrong', 'message_error'=>$e->getMessage(),'request'=>$request->token]);
            }
        }
        return $next($request);
    }
}
