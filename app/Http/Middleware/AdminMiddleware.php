<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Nếu là admin thì cho phép truy cập
        if (Auth::check() && Auth::user()->role == 'admin') {
            return $next($request);
        }

        
        if (!Auth::check()) {
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Vui lòng đăng nhập với tài khoản quản trị viên.',
            ]);
        }

      
        return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập vào trang quản trị viên!');
    }
}
