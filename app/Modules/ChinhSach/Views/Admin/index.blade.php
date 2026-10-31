@extends('Core::Admin.layouts.app')
@section('title', 'Chính sách')
@section('page-title', 'Quản lý Chính sách')
@section('content')
<div class="card">
    <div class="card-header">
        <h5>Chính sách ({{ $items->total() }})</h5>
        <a href="{{ route('admin.chinh-sachs.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Thêm mới</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead><tr><th>#</th><th>Tiêu đề</th><th>URL</th><th>Vị trí web</th><th>Hiển thị</th><th>Thao tác</th></tr></thead>
            <tbody>
                @forelse($items as $i)
                <tr>
                    <td>{{ $i->id }}</td>
                    <td>{{ $i->title }}</td>
                    <td>{{ $i->url ?? '—' }}</td>
                    <td>{{ $i->vi_tri_web }}</td>
                    <td><span class="badge {{ $i->status ? 'badge-success':'badge-danger' }}">{{ $i->status ? 'Hiện':'Ẩn' }}</span></td>
                    <td>
                        <a href="{{ route('admin.chinh-sachs.edit', $i) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.chinh-sachs.destroy', $i) }}" style="display:inline" onsubmit="return confirm('Xóa?')">
                            @csrf @method('DELETE') <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#8a9bb0;padding:40px">Chưa có dữ liệu.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px">{{ $items->links() }}</div>
    </div>
</div>
@endsection
