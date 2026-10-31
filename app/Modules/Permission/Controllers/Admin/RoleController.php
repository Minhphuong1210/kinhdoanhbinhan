<?php

namespace App\Modules\Permission\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:rbac.manage');
    }

    public function index()
    {
        $roles = Role::withCount('permissions')->latest()->paginate(20);
        return view('Permission::Admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('Permission::Admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|max:255|unique:roles,name', 'display_name' => 'nullable|max:255']);
        $role = Role::create($data);
        $role->permissions()->sync($request->permissions ?? []);
        return redirect()->route('admin.roles.index')->with('success', 'Thêm role thành công!');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $role->load('permissions');
        return view('Permission::Admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate(['name' => 'required|max:255|unique:roles,name,'.$role->id, 'display_name' => 'nullable|max:255']);
        $role->update($data);
        $role->permissions()->sync($request->permissions ?? []);
        return redirect()->route('admin.roles.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return back()->with('success', 'Xóa thành công!');
    }
}
