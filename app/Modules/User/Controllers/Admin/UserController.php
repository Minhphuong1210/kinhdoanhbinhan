<?php

namespace App\Modules\User\Controllers\Admin;

use App\Exports\MembersExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Location\Services\LocationService;
use App\Modules\Permission\Services\RoleService;
use App\Modules\User\Requests\UserRequest;
use App\Modules\User\Services\UserService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly RoleService $roleService,
        private readonly LocationService $locations
    ) {
        $this->middleware('permission:user.view')->only(['index', 'export', 'searchUsers']);
        $this->middleware('permission:user.edit')->only(['create', 'store', 'edit', 'update']);
        $this->middleware('permission:user.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $users = $this->userService->list($request->only(['search', 'active', 'is_admin', 'tu_ngay', 'den_ngay']));

        return view('User::Admin.index', compact('users'));
    }

    public function create()
    {
        $roles     = $this->roleService->allOrdered();
        $parents   = $this->userService->parentOptions();
        $provinces = $this->locations->provinces();

        return view('User::Admin.create', compact('roles', 'parents', 'provinces'));
    }

    public function store(UserRequest $request)
    {
        $this->userService->create($request->validated());

        return redirect()->route('admin.users.index')->with('success', 'Tạo tài khoản thành công!');
    }

    public function edit(User $user)
    {
        $roles     = $this->roleService->allOrdered();
        $userRoles = $user->roles->pluck('id')->toArray();
        $parents   = $this->userService->parentOptions($user->id);
        $provinces = $this->locations->provinces();
        $districts = $this->locations->districtsOf($user->province_id);
        $wards     = $this->locations->wardsOf($user->district_id);

        return view('User::Admin.edit', compact('user', 'roles', 'userRoles', 'provinces', 'parents', 'districts', 'wards'));
    }

    public function update(UserRequest $request, User $user)
    {
        $this->userService->update($user, $request->validated());

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật thành viên thành công!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể xóa tài khoản đang đăng nhập!');
        }

        $this->userService->delete($user);

        return back()->with('success', 'Xóa thành viên thành công!');
    }

    public function export(Request $request)
    {
        $tuNgay  = $request->tu_ngay;
        $denNgay = $request->den_ngay;

        $filename = 'danh-sach-thanh-vien';
        if ($tuNgay) {
            $filename .= '-' . $tuNgay;
        }
        if ($denNgay) {
            $filename .= '-' . $denNgay;
        }
        $filename .= '.xlsx';

        return Excel::download(new MembersExport($tuNgay, $denNgay), $filename);
    }

    public function searchUsers(Request $request)
    {
        return response()->json($this->userService->search($request->q));
    }
}
