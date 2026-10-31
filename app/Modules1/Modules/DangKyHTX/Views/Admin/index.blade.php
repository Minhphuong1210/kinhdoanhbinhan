@extends('Core::Admin.layouts.app')
@section('title', 'Đăng ký HTX')
@section('page-title', 'Quản lý đăng ký HTX')
@section('content')
    <div class="card">
        <div class="card-header"><h5>Đăng ký HTX ({{ $items->total() }})</h5></div>
        <div class="card-body" style="padding:0">
            <form method="GET"
                  style="padding:16px 20px;border-bottom:1px solid #f0f2f5;display:flex;gap:10px;flex-wrap:wrap">
                <input type="text" name="search" class="form-control" style="max-width:250px" placeholder="Tên, SĐT..."
                       value="{{ request('search') }}">
                <select name="status" class="form-control" style="max-width:160px">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Chờ duyệt</option>
                    <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Đã duyệt</option>
                    <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Từ chối</option>
                </select>
                <button class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Lọc</button>
            </form>

{{--            <a href="{{ route('admin.dang-ky-htxs.export-all-pdf') }}" class="btn btn-warning btn-sm">--}}
{{--                Export tất cả PDF--}}
{{--            </a>--}}

            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Họ tên</th>
                    <th>SĐT</th>
                    <th>Email</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($items as $i)
                    <tr>
                        <td>{{ $i->id }}</td>
                        <td>{{ $i->full_name }}</td>
                        <td>{{ $i->phone }}</td>
                        <td>{{ $i->email }}</td>
                        <td>
                            <span class="badge {{ $i->status==='approved'?'badge-success':($i->status==='rejected'?'badge-danger':'badge-warning') }}">{{ $i->status==='approved'?'Đã duyệt':($i->status==='rejected'?'Từ chối':'Chờ duyệt') }}</span>
                        </td>
                        <td>{{ $i->created_at?->format('d/m/Y') }}</td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('admin.dang-ky-htxs.show', $i) }}" class="btn btn-primary btn-sm"><i
                                        class="fas fa-eye"></i></a>
                            @if($i->status==='pending'  || $i->status==='0' )
                                <form method="POST" action="{{ route('admin.dang-ky-htxs.update', $i) }}"
                                      style="display:inline">@csrf @method('PUT')<input type="hidden" name="status"
                                                                                        value="approved">
                                    <button class="btn btn-success btn-sm"><i class="fas fa-check"></i></button>
                                </form>
                                <form method="POST" action="{{ route('admin.dang-ky-htxs.update', $i) }}"
                                      style="display:inline">@csrf @method('PUT')<input type="hidden" name="status"
                                                                                        value="rejected">
                                    <button class="btn btn-danger btn-sm"><i class="fas fa-times"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:#8a9bb0;padding:40px">Chưa có đăng ký.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div style="padding:16px 20px">{{ $items->links() }}</div>
        </div>
    </div>
@endsection
