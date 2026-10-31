<?php

namespace App\Modules\Permission\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Permission\Models\Role;
use App\Modules\Permission\Requests\RoleRequest;
use App\Modules\Permission\Services\PermissionService;
use App\Modules\Permission\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly PermissionService $permissionService
    )
    {
        $this->middleware('permission:rbac.manage');
    }

    public function index(Request $request)
    {
        $roles = $this->roleService->list();

        return view('Permission::Admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->permissionService->all();

        return view('Permission::Admin.roles.create', compact('permissions'));
    }

    public function store(RoleRequest $request)
    {
        $this->roleService->create($request->validated());

        return redirect()->route('admin.roles.index')->with('success', 'Thêm role thành công!');
    }

    public function edit(Role $role)
    {
        $permissions = $this->permissionService->all();
        $role->load('permissions');

        return view('Permission::Admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(RoleRequest $request, Role $role)
    {
        $this->roleService->update($role, $request->validated());

        return redirect()->route('admin.roles.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Role $role)
    {
        $this->roleService->delete($role);

        return back()->with('success', 'Xóa thành công!');
    }
}
