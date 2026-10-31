{{-- GioiThieu index --}}
@extends('Core::Admin.layouts.app')
@section('title', 'Giới thiệu')
@section('page-title', 'Quản lý Giới thiệu')
@section('content')
<div class="card">
    <div class="card-header">
        <h5>Giới thiệu ({{ $items->total() }})</h5>
        <a href="{{ route('admin.gioi-thieus.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Thêm mới</a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead><tr><th>#</th><th>Ảnh</th><th>Tên</th><th>Vị trí</th><th>Năm</th><th>Thao tác</th></tr></thead>
            <tbody>
                @forelse($items as $i)
                <tr>
                    <td>{{ $i->id }}</td>
                    <td>@if($i->image)<img src="{{ cdn($i->image) }}" class="img-thumb">@endif</td>
                    <td>{{ $i->name }}</td>
                    <td>{{ $i->vi_tri_web }}</td>
                    <td>{{ $i->year }}</td>
                    <td>
                        <a href="{{ route('admin.gioi-thieus.edit', $i) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.gioi-thieus.destroy', $i) }}" style="display:inline" onsubmit="return confirm('Xóa?')">
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
