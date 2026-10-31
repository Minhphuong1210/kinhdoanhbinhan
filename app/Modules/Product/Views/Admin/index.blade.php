@extends('Core::Admin.layouts.app')
@section('title', 'Sản phẩm')
@section('page-title', 'Quản lý sản phẩm')
@section('content')
<div class="card">
    <div class="card-header">
        <h5>Danh sách sản phẩm ({{ $products->total() }})</h5>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Thêm mới</a>
    </div>
    <div class="card-body" style="padding:0">
        <form method="GET" style="padding:16px 20px;border-bottom:1px solid #f0f2f5;display:flex;gap:10px;flex-wrap:wrap">
            <input type="text" name="search" class="form-control" style="max-width:250px" placeholder="Tìm theo tên..." value="{{ request('search') }}">
            <select name="category_id" class="form-control" style="max-width:200px">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $c)<option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach
            </select>
            <button class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Lọc</button>
            @if(request()->anyFilled(['search','category_id']))<a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">Xóa lọc</a>@endif
        </form>
        <table>
            <thead><tr><th>#</th><th>Ảnh</th><th>Tên sản phẩm</th><th>Danh mục</th><th>Giá</th><th>SL</th>@if(auth()->user()->is_admin)<th>Giá nhập</th>@endif<th>Cơ chế</th><th>Nổi bật</th><th>Hiển thị</th><th>Thao tác</th></tr></thead>
            <tbody>
                @forelse($products as $p)
                <tr>
                    <td>{{ $p->stt }}</td>
                    <td>@if($p->image)<img src="{{ cdn($p->image)  }}" class="img-thumb">@else<div style="width:44px;height:44px;background:#f0f2f5;border-radius:7px;display:flex;align-items:center;justify-content:center;color:#ccc"><i class="fas fa-image"></i></div>@endif</td>
                    <td><strong>{{ $p->name }}</strong><br><small style="color:#8a9bb0">{{ $p->slug }}</small></td>
                    <td>{{ $p->category?->name ?? '—' }}</td>
                    <td>{{ $p->price ? number_format($p->price).'đ' : '—' }}</td>
                    <td>{{ $p->quantity === null ? 'Không GH' : number_format($p->quantity) }}</td>
                    @if(auth()->user()->is_admin)
                        <td>{{ $p->purchase_price !== null ? number_format($p->purchase_price).'đ' : '—' }}</td>
                    @endif
                    <td>{{ $p->coCheHoanPhi?->name ?? 'Setting mặc định' }}</td>
                    <td><span class="badge {{ $p->san_pham_noi_bat ? 'badge-warning' : 'badge-secondary' }}">{{ $p->san_pham_noi_bat ? 'Nổi bật' : '—' }}</span></td>
                    <td><span class="badge {{ $p->active ? 'badge-success' : 'badge-danger' }}">{{ $p->active ? 'Hiện' : 'Ẩn' }}</span></td>
                    <td style="white-space:nowrap">
                        <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.products.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Xóa sản phẩm này?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ auth()->user()->is_admin ? 11 : 10 }}" style="text-align:center;color:#8a9bb0;padding:40px">Chưa có sản phẩm nào.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px">{{ $products->links() }}</div>
    </div>
</div>
@endsection
