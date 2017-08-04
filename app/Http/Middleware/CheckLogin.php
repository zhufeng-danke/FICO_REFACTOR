<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class CheckLogin
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
        if (config('app.env') != 'production'){
            // 非生产环境直接跳过验证

            return $next($request);
        }

        if(!Session::has('corp_user')){
            // 未登录处理

            return redirect(env('LAPUTA_API_URL') . "/fico/jump/?url=" . url()->current());
        }

        return $next($request);
    }
}
