@extends('Core::Admin.layouts.app')
@section('title', 'Danh mục sản phẩm')
@section('page-title', 'Danh mục sản phẩm')
@section('content')
<div class="card">
    <div class="card-header">
        <h5>Danh sách danh mục</h5>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Thêm mới</a>
    </div>
    <div class="card-body" style="padding:0">
        <form method="GET" style="padding:16px 20px;border-bottom:1px solid #f0f2f5;display:flex;gap:10px">
            <input type="text" name="search" class="form-control" style="max-width:300px" placeholder="Tìm kiếm..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Tìm</button>
            @if(request('search'))<a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">Xóa lọc</a>@endif
        </form>
        <table>
            <thead>
                <tr><th>#</th><th>Ảnh</th><th>Tên danh mục</th><th>Danh mục cha</th><th>Trạng thái</th><th>Thao tác</th></tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    <td>@if($cat->image)<img src="{{  cdn($cat->image) }}" class="img-thumb" alt="">@else<div style="width:44px;height:44px;background:#f0f2f5;border-radius:7px;display:flex;align-items:center;justify-content:center;color:#ccc"><i class="fas fa-image"></i></div>@endif</td>
                    <td><strong>{{ $cat->name }}</strong><br><small style="color:#8a9bb0">{{ $cat->slug }}</small></td>
                    <td>{{ $cat->parent?->name ?? '—' }}</td>
                    <td><span class="badge {{ $cat->active ? 'badge-success' : 'badge-danger' }}">{{ $cat->active ? 'Hiện' : 'Ẩn' }}</span></td>
                    <td style="white-space:nowrap">
                        <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" style="display:inline" onsubmit="return confirm('Xóa danh mục này?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#8a9bb0;padding:40px">Chưa có danh mục nào.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px">{{ $categories->links() }}</div>
    </div>
</div>
@endsection
