@extends('Core::Admin.layouts.app')
@section('title', 'Lịch sử nạp tiền')
@section('page-title', 'Lịch sử nạp tiền PayOS')

@section('content')

{{-- Flash messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Thống kê nhanh --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:20px;">
    <div style="background:#fff;border-radius:12px;padding:18px 20px;box-shadow:0 1px 6px rgba(0,0,0,.07);">
        <div style="font-size:11px;color:#8a9bb0;text-transform:uppercase;letter-spacing:.5px">Tổng đơn</div>
        <div style="font-size:24px;font-weight:700;color:#1e293b;margin-top:4px">{{ $items->total() }}</div>
    </div>
    <div style="background:#fff;border-radius:12px;padding:18px 20px;box-shadow:0 1px 6px rgba(0,0,0,.07);">
        <div style="font-size:11px;color:#8a9bb0;text-transform:uppercase;letter-spacing:.5px">Tổng đã nạp thành công</div>
        <div style="font-size:22px;font-weight:700;color:#16a34a;margin-top:4px">{{ number_format($tongTien, 0, ',', '.') }}₫</div>
    </div>
</div>

<div class="card">
    {{-- Header + Filter --}}
    <div class="card-header" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <h5 style="margin:0;flex:1;">Lịch sử nạp tiền</h5>

{{--        <a href="{{ route('admin.nap-tien') }}" class="btn btn-primary btn-sm">--}}
{{--            <i class="fas fa-plus"></i> Nạp tiền--}}
{{--        </a>--}}
{{--        <a href="{{ route('admin.payment-history.cap-nhat') }}" class="btn btn-secondary btn-sm"--}}
{{--           onclick="return confirm('Đồng bộ trạng thái từ PayOS?')">--}}
{{--            <i class="fas fa-sync-alt"></i> Đồng bộ--}}
{{--        </a>--}}
    </div>

    {{-- Filter bar --}}
    <div style="padding:14px 18px;background:#f8fafc;border-bottom:1px solid #e8edf2;">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div>
                <label style="font-size:12px;color:#5a6a7e;display:block;margin-bottom:3px">Trạng thái</label>
                <select name="trang_thai" class="form-control form-control-sm" style="min-width:140px">
                    <option value="">-- Tất cả --</option>
                    @foreach(['Đang xử lý','Thành công','Đã hủy'] as $tt)
                        <option value="{{ $tt }}" {{ request('trang_thai') == $tt ? 'selected' : '' }}>{{ $tt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:12px;color:#5a6a7e;display:block;margin-bottom:3px">Từ ngày</label>
                <input type="date" name="tu_ngay" value="{{ request('tu_ngay') }}" class="form-control form-control-sm">
            </div>
            <div>
                <label style="font-size:12px;color:#5a6a7e;display:block;margin-bottom:3px">Đến ngày</label>
                <input type="date" name="den_ngay" value="{{ request('den_ngay') }}" class="form-control form-control-sm">
            </div>
            <div>
                <label style="font-size:12px;color:#5a6a7e;display:block;margin-bottom:3px">Nội dung</label>
                <input type="text" name="description" value="{{ request('description') }}" class="form-control form-control-sm" placeholder="Tìm nội dung...">
            </div>
            <div style="display:flex;gap:6px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Lọc</button>
                <a href="{{ route('admin.payment-history.index') }}" class="btn btn-light btn-sm"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>

    {{-- Badge filters (trạng thái nhanh) --}}
    <div style="padding:10px 18px;display:flex;gap:8px;flex-wrap:wrap;border-bottom:1px solid #e8edf2;">
        @foreach(['' => 'Tất cả', 'Đang xử lý' => 'Đang xử lý', 'Thành công' => 'Thành công', 'Đã hủy' => 'Đã hủy'] as $val => $label)
            <a href="{{ request()->fullUrlWithQuery(['trang_thai' => $val]) }}"
               class="btn btn-sm {{ request('trang_thai', '') === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="card-body" style="padding:0">
        <div style="overflow-x:auto;">
            <table class="table table-hover" style="margin:0;min-width:800px">
                <thead style="background:#f1f5f9;">
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Ngày tạo</th>
                        <th>Người nạp</th>
                        <th>Số tiền</th>
                        <th>Mã đơn</th>
                        <th>Nội dung</th>
                        <th>Link</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($items as $item)
                    @php




                        [$badgeText, $badgeStyle] = match($item->trang_thai) {
                            'Thành công' => ['Thành công', 'background:#dcfce7;color:#16a34a;'],
                            'Đã hủy'     => ['Đã hủy',     'background:#fee2e2;color:#dc2626;'],
                            default      => ['Đang xử lý', 'background:#fef9c3;color:#ca8a04;'],
                        };
                    @endphp
                    <tr>
                        <td style="color:#8a9bb0;font-size:12px">{{ $item->id }}</td>
                        <td style="font-size:12px;color:#5a6a7e;white-space:nowrap">
                            {{ $item->created_at?->format('d/m/Y') }}<br>
                            <span style="color:#94a3b8">{{ $item->created_at?->format('H:i') }}</span>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:13px">{{ $item->nguoiNap?->name ?? '—' }}</div>
                            <div style="font-size:11px;color:#8a9bb0">{{ $item->nguoiNap?->email }}</div>
                        </td>
                        <td style="font-weight:700;color:#16a34a;white-space:nowrap;font-size:14px">
                            {{ number_format($item->so_tien, 0, ',', '.') }}₫
                        </td>
                        <td style="font-family:monospace;font-size:12px">{{ $item->ma_don }}</td>
                        <td style="font-size:12px;color:#5a6a7e">{{ $item->description }}</td>
                        <td>
                            @if($item->link)
                                <a href="{{ $item->link }}" target="_blank" class="btn btn-outline-primary btn-sm" style="font-size:11px">
                                    <i class="fas fa-external-link-alt"></i> Mở
                                </a>
                            @else
                                <span style="color:#cbd5e1">—</span>
                            @endif
                        </td>
                        <td>
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;{{ $badgeStyle }}">

                               

                                {{ $badgeText }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;color:#94a3b8;padding:50px;">
                            <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:10px"></i>
                            Chưa có lịch sử nạp tiền.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:16px 20px;">{{ $items->links() }}</div>
    </div>
</div>

@endsection
