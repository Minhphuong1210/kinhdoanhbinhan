@extends('Core::Admin.layouts.app')
@section('title', 'Permissions')
@section('page-title', 'Quản lý Permissions')
@section('content')
<div class="card">
    <div class="card-header">
        <h5>Danh sách Permissions</h5>
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Thêm mới</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead><tr><th>#</th><th>Tên (slug)</th><th>Display Name</th><th>Thao tác</th></tr></thead>
            <tbody>
                @forelse($permissions as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td><code>{{ $p->name }}</code></td>
                    <td>{{ $p->display_name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('admin.permissions.edit', $p) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.permissions.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Xóa?')">
                            @csrf @method('DELETE') <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:#8a9bb0;padding:40px">Chưa có permissions.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px">{{ $permissions->links() }}</div>
    </div>
</div>
@endsection
