<?php

namespace App\Http\Middleware;

use Closure;

class EnsureEmailIsVerified
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
        // 三个判断条件
        // 1.如果用户已经登录
        // 2.并且未认证
        // 3.并且访问的不是Email验证相关的url或者退出的url
        if ($request->user() &&
            ! $request->user()->hasVerifiedEmail() &&
            ! $request->is('email/*','logout')) {
            // 根据客户端返回对应内容
            return $request->expectsJson() ? abort(403,'你的邮箱没有激活!')
                : redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
