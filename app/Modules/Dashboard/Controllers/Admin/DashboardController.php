<?php

namespace App\Modules\Dashboard\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Dashboard\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->is_admin;

        $data = $isAdmin
            ? $this->dashboardService->adminData($request->from_date, $request->to_date)
            : $this->dashboardService->memberData($user);

        return view('Core::Admin.dashboard.index', compact('isAdmin', 'user') + $data);
    }
}
