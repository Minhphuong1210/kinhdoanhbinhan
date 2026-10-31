@extends('Core::Admin.layouts.app')
@section('title', 'Lịch sử Admin rút tiền')
@section('page-title', 'Lịch sử Admin rút tiền')
@section('content')

    <div class="card" style="border:none;border-radius:16px;box-shadow:0 4px 24px rgba(37,99,235,0.08);overflow:hidden">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;background:linear-gradient(135deg,#1e40af,#3b82f6);color:#fff;border:none;padding:18px 24px">
            <h5 style="margin:0;font-weight:700;font-size:16px">
                <i class="fas fa-history"></i> Lịch sử Admin rút tiền ({{ $items->total() }})
            </h5>
            <a href="{{ route('admin.admin-rut-tien.create') }}"
               style="background:rgba(255,255,255,0.2);color:#fff;padding:6px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all 0.2s;display:inline-flex;align-items:center;gap:6px">
                <i class="fas fa-plus-circle"></i> Rút tiền mới
            </a>
        </div>

        {{-- Search --}}
        <div class="card-body" style="border-bottom:1px solid #e5e7eb;padding:14px 24px">
            <form method="GET" action="{{ route('admin.admin-rut-tien.history') }}"
                  style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
                <div style="flex:1;min-width:200px">
                    <input type="text" name="search" class="form-control form-control-sm"
                           value="{{ request('search') }}"
                           placeholder="Tìm theo tên, SĐT, ghi chú...">
                </div>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-search"></i> Tìm
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.admin-rut-tien.history') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-times"></i> Xóa lọc
                    </a>
                @endif
            </form>
        </div>

        <div class="card-body" style="padding:0;overflow-x:auto">
            @if($items->count())
                <table class="table" style="margin:0;min-width:700px">
                    <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Thời gian</th>
                        <th>Thành viên</th>
                        <th>Nội dung</th>
                        <th style="text-align:right">Số tiền</th>
                        <th style="text-align:right">Số dư trước</th>
                        <th style="text-align:right">Số dư sau</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td style="font-size:12px;color:#8a9bb0">{{ $item->id }}</td>
                            <td style="font-size:12px;color:#8a9bb0;white-space:nowrap">
                                {{ $item->created_at?->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                @if($item->user)
                                    <div style="font-weight:600;font-size:13px">{{ $item->user->name }}</div>
                                    <div style="font-size:11px;color:#8a9bb0">{{ $item->user->tel }}</div>
                                @else
                                    <span style="color:#c4d0e0">—</span>
                                @endif
                            </td>
                            <td style="font-size:13px;max-width:300px">
                                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:6px 10px;font-size:12px;color:#991b1b">
                                    <i class="fas fa-info-circle" style="margin-right:4px;color:#dc2626"></i>
                                    {{ $item->note }}
                                </div>
                            </td>
                            <td style="text-align:right;font-weight:700;font-size:14px;color:#dc2626;white-space:nowrap">
                                -{{ number_format($item->amount, 0, ',', '.') }}₫
                            </td>
                            <td style="text-align:right;font-size:13px;color:#5a6a7e;white-space:nowrap">
                                {{ number_format($item->surplus_before ?? 0, 0, ',', '.') }}₫
                            </td>
                            <td style="text-align:right;font-size:13px;color:#5a6a7e;white-space:nowrap">
                                {{ number_format($item->surplus ?? 0, 0, ',', '.') }}₫
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div style="padding:16px 24px">{{ $items->links() }}</div>
            @else
                <div style="text-align:center;padding:60px;color:#8a9bb0">
                    <i class="fas fa-inbox" style="font-size:42px;display:block;margin-bottom:12px;opacity:.3"></i>
                    <div style="font-size:14px">Chưa có giao dịch rút tiền nào bởi Admin.</div>
                </div>
            @endif
        </div>
    </div>

@endsection
