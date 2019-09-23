<?php

namespace App\Http\Middleware;

use App\Tools\TokenTool;
use Closure;

class TokenMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader('token') || !$request->hasHeader('token_type'))
            return response()->json([
                'code' => 1010,
                'message' => '未提交token或者token_type'
            ]);
        $checkRes = TokenTool::isTokenExist($request->header('token'));
        if (!$checkRes)
            return response()->json([
                'code' => 1011,
                'message' => '该token不存在'
            ]);
        $checkRes = TokenTool::isTokenExpired($request->header('token'));
        if ($checkRes)
            return response()->json([
                'code' => 1012,
                'message' => 'token已过期，请重新登录'
            ]);
        else {
            $userInfo = TokenTool::getUserByToken($request->header('token'), $request->header('token_type'));
            $request->user = $userInfo;
            return $next($request);
        }
    }
}
