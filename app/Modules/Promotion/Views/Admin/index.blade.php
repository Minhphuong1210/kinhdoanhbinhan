@extends('Core::Admin.layouts.app')
@section('title', 'Quản lý Khuyến mãi')
@section('page-title', 'Sự kiện Khuyến mãi')

@push('styles')
<style>
    .promo-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px}
    .promo-header h2{font-size:20px;font-weight:700;color:#1e2a3a;margin:0}
    .btn-add{padding:9px 18px;background:linear-gradient(135deg,#4e8ef7,#6c63ff);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:.2s;display:inline-flex;align-items:center;gap:6px}
    .btn-add:hover{opacity:.9;transform:translateY(-1px);box-shadow:0 4px 15px rgba(78,142,247,.3);color:#fff}

    .promo-table{width:100%;border-collapse:separate;border-spacing:0;background:#fff;border-radius:12px;overflow:hidden;border:1.5px solid #e2e8f0}
    .promo-table th{background:#f8faff;padding:12px 16px;font-size:11px;color:#8a9bb0;text-transform:uppercase;letter-spacing:.5px;text-align:left;border-bottom:1.5px solid #e2e8f0}
    .promo-table td{padding:14px 16px;font-size:13px;color:#1e2a3a;border-bottom:1px solid #f1f5f9}
    .promo-table tr:last-child td{border-bottom:none}
    .promo-table tr:hover td{background:#fafbff}

    .badge-active{background:#dcfce7;color:#16a34a;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
    .badge-inactive{background:#f1f5f9;color:#94a3b8;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
    .badge-month{background:#eff6ff;color:#3b82f6;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
    .badge-count{background:#fef3c7;color:#d97706;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}

    .actions{display:flex;gap:8px}
    .btn-edit{padding:5px 12px;background:#f0f9ff;color:#3b82f6;border:1px solid #bfdbfe;border-radius:6px;font-size:12px;text-decoration:none;transition:.2s}
    .btn-edit:hover{background:#3b82f6;color:#fff}
    .btn-del{padding:5px 12px;background:#fef2f2;color:#ef4444;border:1px solid #fecaca;border-radius:6px;font-size:12px;cursor:pointer;transition:.2s}
    .btn-del:hover{background:#ef4444;color:#fff}

    .empty-state{text-align:center;padding:60px 20px;color:#94a3b8}
    .empty-state i{font-size:48px;margin-bottom:12px;display:block;opacity:.4}
    .empty-state p{font-size:15px;margin-bottom:16px}
</style>
@endpush

@section('content')
    <div class="promo-header">
        <h2><i class="fas fa-gift" style="color:#6c63ff"></i> Sự kiện Khuyến mãi</h2>
        <a href="{{ route('admin.promotions.create') }}" class="btn-add">
            <i class="fas fa-plus"></i> Tạo sự kiện mới
        </a>
    </div>

    @if($promotions->count())
        <table class="promo-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Tên sự kiện</th>
                <th>Tháng</th>
                <th>Số SP</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th style="text-align:center">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            @foreach($promotions as $promo)
                <tr>
                    <td>{{ $promo->id }}</td>
                    <td><b>{{ $promo->name }}</b></td>
                    <td><span class="badge-month">{{ $promo->month_label }}</span></td>
                    <td><span class="badge-count">{{ $promo->product_promotions_count }} SP</span></td>
                    <td>
                        @if($promo->is_active)
                            <span class="badge-active"><i class="fas fa-check-circle"></i> Active</span>
                        @else
                            <span class="badge-inactive"><i class="fas fa-pause-circle"></i> Tắt</span>
                        @endif
                    </td>
                    <td>{{ $promo->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="actions" style="justify-content:center">
                            <a href="{{ route('admin.promotions.edit', $promo->id) }}" class="btn-edit">
                                <i class="fas fa-pen"></i> Sửa
                            </a>
                            <form action="{{ route('admin.promotions.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Xoá sự kiện này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-del"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div style="margin-top:16px">{{ $promotions->links() }}</div>
    @else
        <div class="empty-state">
            <i class="fas fa-gift"></i>
            <p>Chưa có sự kiện khuyến mãi nào.</p>
            <a href="{{ route('admin.promotions.create') }}" class="btn-add">
                <i class="fas fa-plus"></i> Tạo sự kiện đầu tiên
            </a>
        </div>
    @endif
@endsection
