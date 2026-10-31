@extends('Core::Admin.layouts.app')
@section('title', 'Roles')
@section('page-title', 'Quản lý Roles')
@section('content')
<div class="card">
    <div class="card-header">
        <h5>Danh sách Roles</h5>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Thêm mới</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead><tr><th>#</th><th>Tên</th><th>Display Name</th><th>Permissions</th><th>Thao tác</th></tr></thead>
            <tbody>
                @forelse($roles as $r)
                <tr>
                    <td>{{ $r->id }}</td>
                    <td><code>{{ $r->name }}</code></td>
                    <td>{{ $r->display_name ?? '—' }}</td>
                    <td>{{ $r->permissions_count }}</td>
                    <td>
                        <a href="{{ route('admin.roles.edit', $r) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.roles.destroy', $r) }}" style="display:inline" onsubmit="return confirm('Xóa?')">
                            @csrf @method('DELETE') <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;color:#8a9bb0;padding:40px">Chưa có roles.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px">{{ $roles->links() }}</div>
    </div>
</div>
@endsection
