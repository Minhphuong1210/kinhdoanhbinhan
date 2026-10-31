<?php

namespace App\Modules\Permission\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::latest()->paginate(30);
        return view('Permission::Admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('Permission::Admin.permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|max:255|unique:permissions,name', 'display_name' => 'nullable|max:255']);
        Permission::create($data);
        return redirect()->route('admin.permissions.index')->with('success', 'Thêm permission thành công!');
    }

    public function edit(Permission $permission)
    {
        return view('Permission::Admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate(['name' => 'required|max:255|unique:permissions,name,'.$permission->id, 'display_name' => 'nullable|max:255']);
        $permission->update($data);
        return redirect()->route('admin.permissions.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return back()->with('success', 'Xóa thành công!');
    }
}
