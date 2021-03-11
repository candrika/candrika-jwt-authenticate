<?php
namespace App\Http\Middleware;
use Closure;
use Exception;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class JwtMiddleware {

    public function handle($request, Closure $next, $guard=null){
        $token = $request->header('x-token');
        if(!$token){
            return response()->json([
                'error'=>'Token tidak tersedia.'
            ],401);
        }

        try{
            $credencial = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        }catch(ExpiredException $e){
            return response()->json([
                'error'=>'Token kadaluarsa'
            ],400);
        }catch(Exception $e){
            return response()->json([
                'error'=>'Error saat decode token'
            ],400);
        }

        $user = User::find($credencial->user_id);
        $request->auth = $user;

        return $next($request);
    }
}