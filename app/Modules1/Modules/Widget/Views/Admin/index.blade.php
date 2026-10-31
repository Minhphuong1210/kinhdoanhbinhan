@extends('Core::Admin.layouts.app')
@section('title', 'Widgets')
@section('page-title', 'Quản lý Widgets')
@section('content')
<div class="card">
    <div class="card-header"><h5>Widgets ({{ $items->total() }})</h5><a href="{{ route('admin.widgets.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Thêm mới</a></div>
    <div class="card-body" style="padding:0">
        <table>
            <thead><tr><th>#</th><th>Vị trí</th><th>Tiêu đề</th><th>Thứ tự</th><th>Hiển thị</th><th>Thao tác</th></tr></thead>
            <tbody>
                @forelse($items as $i)
                <tr><td>{{ $i->id }}</td><td><code>{{ $i->location }}</code></td><td>{{ $i->title }}</td><td>{{ $i->order_no }}</td><td><span class="badge {{ $i->status ? 'badge-success':'badge-danger' }}">{{ $i->status ? 'Hiện':'Ẩn' }}</span></td><td><a href="{{ route('admin.widgets.edit', $i) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a><form method="POST" action="{{ route('admin.widgets.destroy', $i) }}" style="display:inline" onsubmit="return confirm('Xóa?')">@csrf @method('DELETE') <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form></td></tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#8a9bb0;padding:40px">Chưa có widgets.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px">{{ $items->links() }}</div>
    </div>
</div>
@endsection
