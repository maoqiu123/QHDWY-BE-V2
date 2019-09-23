<?php

namespace App\Http\Middleware;

use App\Tools\TokenTool;
use Closure;

class YzzhMiddleware
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
        if (!$request->hasHeader('yzzh_token'))
            return response()->json([
                'code' => 1010,
                'message' => '未提交yzzh_token'
            ]);
        $checkRes = TokenTool::isYzzhTokenExist($request->header('yzzh_token'));
        if (!$checkRes)
            return response()->json([
                'code' => 1011,
                'message' => '该yzzh_token不存在'
            ]);
        $checkRes = TokenTool::isYzzhTokenExpired($request->header('yzzh_token'));
        if ($checkRes)
            return response()->json([
                'code' => 1012,
                'message' => 'yzzh_token已过期，请重新登录'
            ]);
        else {
            return $next($request);
        }
    }
}
