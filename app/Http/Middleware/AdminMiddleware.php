<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('admin.login')->with('error', 'Vui lòng đăng nhập.');
        }

        // Tất cả user đăng nhập đều được vào — sidebar/controller tự lọc quyền
        return $next($request);
    }
}
