@extends('Core::Admin.layouts.app')
@section('title', 'Danh mục bài viết')
@section('page-title', 'Danh mục bài viết')
@section('content')
<div class="card">
    <div class="card-header">
        <h5>Danh sách danh mục ({{ $categories->total() }})</h5>
        <a href="{{ route('admin.category-posts.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Thêm mới</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead><tr><th>#</th><th>Tên</th><th>Slug</th><th>Bài viết</th><th>Hiển thị</th><th>Thao tác</th></tr></thead>
            <tbody>
                @forelse($categories as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td><strong>{{ $c->name }}</strong></td>
                    <td><small style="color:#8a9bb0">{{ $c->slug }}</small></td>
                    <td>{{ $c->posts_count }}</td>
                    <td><span class="badge {{ $c->active ? 'badge-success' : 'badge-danger' }}">{{ $c->active ? 'Hiện' : 'Ẩn' }}</span></td>
                    <td>
                        <a href="{{ route('admin.category-posts.edit', $c) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.category-posts.destroy', $c) }}" style="display:inline" onsubmit="return confirm('Xóa?')">
                            @csrf @method('DELETE') <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#8a9bb0;padding:40px">Chưa có dữ liệu.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px">{{ $categories->links() }}</div>
    </div>
</div>
@endsection
