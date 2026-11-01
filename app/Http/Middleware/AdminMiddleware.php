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

        // Tài khoản bị khoá thì đăng xuất ngay, kể cả đang có phiên đăng nhập
        if (!$user->active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $request->expectsJson()
                ? response()->json(['message' => 'Tai khoan da bi khoa.'], 403)
                : redirect()->route('admin.login')->with('error', 'Tài khoản của bạn đã bị khoá.');
        }

        // Tất cả user đăng nhập đều được vào — sidebar/controller tự lọc quyền
        return $next($request);
    }
}
