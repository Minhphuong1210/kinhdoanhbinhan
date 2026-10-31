<?php

namespace App\Modules\Permission\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Permission\Models\Permission;
use App\Modules\Permission\Requests\PermissionRequest;
use App\Modules\Permission\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(private readonly PermissionService $permissionService)
    {
        $this->middleware('permission:rbac.manage');
    }

    public function index(Request $request)
    {
        $permissions = $this->permissionService->list();

        return view('Permission::Admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('Permission::Admin.permissions.create');
    }

    public function store(PermissionRequest $request)
    {
        $this->permissionService->create($request->validated());

        return redirect()->route('admin.permissions.index')->with('success', 'Thêm permission thành công!');
    }

    public function edit(Permission $permission)
    {
        return view('Permission::Admin.permissions.edit', compact('permission'));
    }

    public function update(PermissionRequest $request, Permission $permission)
    {
        $this->permissionService->update($permission, $request->validated());

        return redirect()->route('admin.permissions.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Permission $permission)
    {
        $this->permissionService->delete($permission);

        return back()->with('success', 'Xóa thành công!');
    }
}
