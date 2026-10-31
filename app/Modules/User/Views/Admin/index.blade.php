@extends('Core::Admin.layouts.app')
@section('title', 'Thành viên')
@section('page-title', 'Quản lý thành viên')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Danh sách thành viên ({{ $users->total() }})</h5>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-user-plus"></i>
                Thêm thành viên</a>
        </div>
        <div class="card-body" style="padding:0">
            <form method="GET" id="filter-form"
                  style="padding:16px 20px;border-bottom:1px solid #f0f2f5;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
                <div class="form-group mb-0">
                    <label style="font-size:12px;margin-bottom:4px;display:block">Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" style="max-width:200px"
                           placeholder="Tên, email, SĐT..." value="{{ request('search') }}">
                </div>
                <div class="form-group mb-0">
                    <label style="font-size:12px;margin-bottom:4px;display:block">Trạng thái</label>
                    <select name="active" class="form-control" style="max-width:140px">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('active') === '1' ? 'selected':'' }}>Hoạt động</option>
                        <option value="0" {{ request('active') === '0' ? 'selected':'' }}>Bị khóa</option>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label style="font-size:12px;margin-bottom:4px;display:block">Từ ngày</label>
                    <input type="date" name="tu_ngay" class="form-control" value="{{ request('tu_ngay') }}">
                </div>
                <div class="form-group mb-0">
                    <label style="font-size:12px;margin-bottom:4px;display:block">Đến ngày</label>
                    <input type="date" name="den_ngay" class="form-control" value="{{ request('den_ngay') }}">
                </div>
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Lọc</button>
                    <button type="button" onclick="exportExcel()" class="btn btn-success btn-sm"><i
                                class="fas fa-file-excel"></i> Xuất Excel
                    </button>
                </div>
            </form>

            <script>
                function exportExcel() {
                    const form = document.getElementById('filter-form');
                    const baseUrl = "{{ route('admin.users.export') }}";
                    const params = new URLSearchParams(new FormData(form)).toString();
                    window.location.href = baseUrl + '?' + params;
                }
            </script>
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>SĐT</th>

                    <th>Ngày sinh</th>
                    <th>Giới tính</th>
                    <th>Quốc tịch</th>
                    <th>CCCD</th>
                    <th>Ngày cấp</th>

                    <th>Tiền ví rút</th>
                    <th>Tiền ví mua hàng</th>
                    <th>Admin</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $u)

                    @php

                        $htx = $u->dangKyXacNhanHtx;



                    @endphp


                    <tr>
                        <td>{{ $u->id }}</td>
                        <td><strong>{{ $u->name }}</strong></td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->tel ?? '—' }}</td>

                       

                        <td>
                            {{ !empty($htx?->birthday)
                                ? \Carbon\Carbon::parse($htx->birthday)->format('d/m/Y')
                                : '—' }}
                        </td>

                        <td>{{ $htx->gender ?? '—' }}</td>

                        <td>{{ $htx->nationality ?? '—' }}</td>

                        <td>{{ $htx->identity_number ?? '—' }}</td>

                        <td>
                            {{ !empty($htx?->identity_date)
                                ? \Carbon\Carbon::parse($htx->identity_date)->format('d/m/Y')
                                : '—' }}
                        </td>




                        <td>{{ $u->vi_tien  }}</td>
                        <td>{{ $u->vi_tien_mua_hang  }}</td>
                        <td>
                            <span class="badge {{ $u->is_admin ? 'badge-info' : 'badge-secondary' }}">{{ $u->is_admin ? 'Admin' : 'User' }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $u->active ? 'badge-success' : 'badge-danger' }}">{{ $u->active ? 'Hoạt động' : 'Bị khóa' }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-warning btn-sm"><i
                                        class="fas fa-edit"></i></a>
                            @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                      style="display:inline" onsubmit="return confirm('Xóa?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;color:#8a9bb0;padding:40px">Chưa có thành viên.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div style="padding:16px 20px">{{ $users->links() }}</div>
        </div>
    </div>
@endsection
