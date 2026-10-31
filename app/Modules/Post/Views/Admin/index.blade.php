@extends('Core::Admin.layouts.app')
@section('title', 'Quản lý bài viết')
@section('page-title', 'Quản lý bài viết')
@section('content')
<div class="card">
    <div class="card-header">
        <h5>Danh sách bài viết ({{ $posts->total() }})</h5>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Thêm mới</a>
    </div>
    <div class="card-body" style="padding:0">
        <form method="GET" style="padding:16px 20px;border-bottom:1px solid #f0f2f5;display:flex;gap:10px;flex-wrap:wrap">
            <input type="text" name="search" class="form-control" style="max-width:250px" placeholder="Tìm theo tên..." value="{{ request('search') }}">
            <select name="category_post_id" class="form-control" style="max-width:200px">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $c)<option value="{{ $c->id }}" {{ request('category_post_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach
            </select>
            <button class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Lọc</button>
            @if(request()->anyFilled(['search','category_post_id']))<a href="{{ route('admin.posts.index') }}" class="btn btn-secondary btn-sm">Xóa lọc</a>@endif
        </form>
        <table>
            <thead><tr><th>#</th><th>Ảnh</th><th>Tiêu đề</th><th>Danh mục</th><th>Trạng thái</th><th>Nổi bật</th><th>Thao tác</th></tr></thead>
            <tbody>
                @forelse($posts as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>@if($p->image)<img src="{{ cdn($p->image)  }}" class="img-thumb">@else<div style="width:44px;height:44px;background:#f0f2f5;border-radius:7px;display:flex;align-items:center;justify-content:center;color:#ccc"><i class="fas fa-image"></i></div>@endif</td>
                    <td><strong>{{ $p->name }}</strong><br><small style="color:#8a9bb0">{{ $p->slug }}</small></td>
                    <td>{{ $p->category?->name ?? '—' }}</td>
                    <td><span class="badge {{ $p->status === 'published' ? 'badge-success' : 'badge-warning' }}">{{ $p->status === 'published' ? 'Đã đăng' : 'Nháp' }}</span></td>
                    <td><span class="badge {{ $p->featured ? 'badge-info' : 'badge-secondary' }}">{{ $p->featured ? 'Nổi bật' : '—' }}</span></td>
                    <td>
                        <a href="{{ route('admin.posts.edit', $p) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.posts.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Xóa bài viết này?')">
                            @csrf @method('DELETE') <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:#8a9bb0;padding:40px">Chưa có bài viết nào.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px">{{ $posts->links() }}</div>
    </div>
</div>
@endsection
