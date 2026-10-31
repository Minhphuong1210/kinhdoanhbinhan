@extends('Core::Admin.layouts.app')
@section('title', 'Tags')
@section('page-title', 'Quản lý Tags')
@section('content')
<div class="card">
    <div class="card-header">
        <h5>Danh sách Tags</h5>
        <a href="{{ route('admin.tags.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Thêm mới</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead><tr><th>#</th><th>Tên</th><th>Slug</th><th>Sản phẩm</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
            <tbody>
                @forelse($tags as $t)
                <tr>
                    <td>{{ $t->id }}</td>
                    <td><strong>{{ $t->name }}</strong></td>
                    <td><small style="color:#8a9bb0">{{ $t->slug }}</small></td>
                    <td>{{ $t->products_count }}</td>
                    <td><span class="badge {{ $t->status == 1 ? 'badge-success' : 'badge-danger' }}">{{ $t->status == 1 ? 'Hoạt động' : 'Ẩn' }}</span></td>
                    <td>
                        <a href="{{ route('admin.tags.edit', $t) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.tags.destroy', $t) }}" style="display:inline" onsubmit="return confirm('Xóa?')">
                            @csrf @method('DELETE') <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#8a9bb0;padding:40px">Chưa có tags.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px">{{ $tags->links() }}</div>
    </div>
</div>
@endsection
