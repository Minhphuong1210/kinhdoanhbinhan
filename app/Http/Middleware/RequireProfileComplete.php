<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireProfileComplete
{
    /**
     * Nếu user chưa cập nhật profile từ sau ngày được cấu hình trong
     * PROFILE_UPDATE_DEADLINE (env), sẽ bắt cập nhật lại.
     * Sau khi user submit form → updated_at sẽ >= deadline → middleware tự bỏ qua.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedRoutes = [
            'admin.profile.edit',
            'admin.profile.update',
            'admin.logout',
        ];

        if (in_array($request->route()?->getName(), $allowedRoutes)) {
            return $next($request);
        }

        $user     = auth()->user();
        $deadline = Carbon::parse(config('app.profile_update_deadline', '2026-03-20'))->startOfDay();

        // Nếu updated_at còn null hoặc trước deadline → bắt cập nhật
        if (!$user->updated_at || $user->updated_at->lt($deadline)) {
            return redirect()->route('admin.profile.edit')
                ->with('info', 'Vui lòng cập nhật thông tin địa chỉ của bạn để tiếp tục sử dụng hệ thống.');
        }

        return $next($request);
    }
}
