@extends('Core::Admin.layouts.app')
@section('title', 'Yêu cầu rút tiền')
@section('page-title', 'Yêu cầu rút tiền')
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<div class="card">
    <div class="card-header" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <h5 style="margin:0;flex:1">Yêu cầu rút tiền ({{ $items->total() }})</h5>
        <div style="display:flex;gap:6px;flex-wrap:wrap">
            @foreach(['' => 'Tất cả', '0' => 'Chờ duyệt', '1' => 'Đã duyệt', '2' => 'Từ chối'] as $val => $label)
            <a href="{{ request()->fullUrlWithQuery(['status' => $val]) }}"
               class="btn btn-sm {{ request('status', '') === $val ? 'btn-primary' : 'btn-secondary' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>

    <div class="card-body" style="border-bottom:1px solid #e5e7eb;padding:12px 20px">
        <form method="GET" action="{{ route('admin.yeu-cau-rut-tien.index') }}"
              style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">

            @if(request('status') !== null && request('status') !== '')
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

                <div style="display:flex;flex-direction:column;gap:4px">
                    <label style="font-size:12px;font-weight:600;color:#374151">Từ ngày</label>
                    <input type="text"
                           id="date_from"
                           name="date_from"
                           value="{{ request('date_from') }}"
                           class="form-control form-control-sm"
                           style="width:150px">
                </div>

                <div style="display:flex;flex-direction:column;gap:4px">
                    <label style="font-size:12px;font-weight:600;color:#374151">Đến ngày</label>
                    <input type="text"
                           id="date_to"
                           name="date_to"
                           value="{{ request('date_to') }}"
                           class="form-control form-control-sm"
                           style="width:150px">
                </div>

            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fas fa-filter"></i> Lọc
            </button>
            @if(request('date_from') || request('date_to'))
                <a href="{{ route('admin.yeu-cau-rut-tien.index', array_filter(['status' => request('status')])) }}"
                   class="btn btn-sm btn-secondary">
                    <i class="fas fa-times"></i> Xoá lọc
                </a>
            @endif

            {{-- Nút xuất Excel (giữ nguyên bộ lọc hiện tại) --}}
            <a href="{{ route('admin.yeu-cau-rut-tien.export-excel', request()->only('date_from','date_to','status')) }}"
               class="btn btn-sm btn-success" style="margin-left:auto">
                <i class="fas fa-file-excel"></i> Xuất Excel
            </a>
        </form>
    </div>


    <div class="card-body" style="padding:0">
        <table class="table" style="margin:0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Thành viên</th>
                    <th>CCCC</th>
                    <th>Số tiền</th>
                    <th>Số tiền cẩn trả</th>
                    <th>Ngân hàng  </th>
                    <th>Chi nhánh ngân hàng  </th>

                    <th>Số TK / Chủ TK</th>
                    <th>Số dư lúc tạo</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            @forelse($items as $i)
            @php
                $statusCls = match((int)$i->status) {
                    0 => ['Chờ duyệt', 'badge-members'],
                    1 => ['Đã duyệt',  'badge-active'],
                    2 => ['Từ chối',   'badge-locked'],
                    default => ['?', ''],
                };
            @endphp
            <tr>
                <td style="color:#8a9bb0;font-size:12px">{{ $i->id }}</td>
                <td>
                    <div style="font-weight:600;font-size:13px">{{ $i->user?->name ?? '—' }}</div>
                    <div style="font-size:11px;color:#8a9bb0">{{ $i->user?->tel }}</div>
                </td>
                <td style="font-weight:700;white-space:nowrap">{{ $i->cccd ?? '' }}</td>
                <td style="font-weight:700;color:#dc2626;white-space:nowrap">{{ number_format($i->so_tien, 0, ',', '.') }}₫</td>

                <?php
                    $so_tien_can_tra = $i->so_tien - $i->so_tien_sau_thue


                    ?>

                <td style="font-weight:700;color:#dc2626;white-space:nowrap">{{ number_format($so_tien_can_tra, 0, ',', '.') }}₫</td>

                <td style="font-size:13px">{{ $i->ngan_hang }}</td>
                <td style="font-size:13px">
                    <div style="font-weight:600">
                        {{ $i->nganHang->name ?? '—' }}
                    </div>
                    <div style="font-size:11px;color:#8a9bb0">
                        {{ $i->nganHang->thanhPho->name ?? '' }}
                    </div>
                </td>
                <td style="font-size:12px;font-family:monospace">
                    {{ $i->stk }}<br>
                    <span style="color:#8a9bb0">{{ $i->chu_tk }}</span>
                </td>
                <td style="font-size:12px;color:#5a6a7e;white-space:nowrap">{{ number_format($i->cast_now ?? 0, 0, ',', '.') }}₫</td>
                <td>
                    <span class="{{ $statusCls[1] }}" style="padding:2px 10px;border-radius:6px;font-size:11px;font-weight:700">
                        {{ $statusCls[0] }}
                    </span>
                </td>
                <td style="font-size:12px;color:#8a9bb0;white-space:nowrap">{{ $i->created_at?->format('d/m/Y H:i') }}</td>
                <td>
                    @if((int)$i->status === 0)
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        <form method="POST" action="{{ route('admin.yeu-cau-rut-tien.update', $i) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Duyet yeu cau rut tien cua {{ $i->user?->name }}?')">
                                <i class="fas fa-check"></i> Duyệt
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.yeu-cau-rut-tien.update', $i) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tu choi va hoan tien {{ number_format($i->so_tien,0,'.',',') }} ve vi?')">
                                <i class="fas fa-times"></i> Từ chối
                            </button>
                        </form>
                    </div>
                    @else
                    <span style="color:#8a9bb0;font-size:12px">Đã xử lý</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;color:#8a9bb0;padding:40px">Chưa có yêu cầu.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px">{{ $items->links() }}</div>
    </div>
</div>


        <script>
            flatpickr("#date_from", {
            locale: "vn",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
        });

            flatpickr("#date_to", {
            locale: "vn",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
        });
    </script>
  

@endsection
