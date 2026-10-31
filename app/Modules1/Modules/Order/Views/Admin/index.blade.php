@extends('Core::Admin.layouts.app')
@section('title', 'Đơn hàng')
@section('page-title', 'Quản lý đơn hàng')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Danh sách đơn hàng ({{ $orders->total() }})</h5>
        </div>
        <div class="card-body" style="padding:0">

            {{-- Bộ lọc --}}
            <form method="GET" id="filterForm"
                  style="padding:14px 18px;border-bottom:1px solid #f0f2f5;display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                <input type="text" name="search" class="form-control" style="max-width:220px"
                       placeholder="Tìm mã đơn/tên..." value="{{ request('search') }}">

                <select name="status" class="form-control" style="max-width:160px">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" {{ request('status') === 'pending'    ? 'selected' : '' }}>Chờ xác nhận
                    </option>
                    <option value="confirmed" {{ request('status') === 'confirmed'  ? 'selected' : '' }}>Đã xác nhận
                    </option>
                    <option value="shipping" {{ request('status') === 'shipping'   ? 'selected' : '' }}>Đang giao
                    </option>
                    <option value="completed" {{ request('status') === 'completed'  ? 'selected' : '' }}>Hoàn thành
                    </option>
                    <option value="cancelled" {{ request('status') === 'cancelled'  ? 'selected' : '' }}>Đã hủy</option>
                </select>

                @if($isAdmin && isset($products))
                    <select name="product_id" class="form-control" style="max-width:200px">
                        <option value="">Tất cả Combo</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ (int)request('product_id') === $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                @endif

                @if($isAdmin)
                    {{-- Chọn ngày --}}
                    <input type="date" name="date_from" class="form-control" style="max-width:160px"
                           value="{{ request('date_from') }}" title="Từ ngày">
                    <span style="color:#8a9bb0;font-size:13px">→</span>
                    <input type="date" name="date_to" class="form-control" style="max-width:160px"
                           value="{{ request('date_to') }}" title="Đến ngày">
                @endif

                <button class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Lọc</button>

                @if($isAdmin)
                    {{-- Nút xuất Excel (CSV) — truyền filter hiện tại --}}
                    <a href="{{ route('admin.orders.export') }}?{{ http_build_query(request()->only('date_from','date_to','status','product_id')) }}"
                       class="btn btn-success btn-sm" style="margin-left:auto" target="_blank">
                        <i class="fas fa-file-excel"></i> Xuất Excel
                    </a>
                @endif
            </form>

            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Mã đơn</th>
                    <th>Khách hàng đặt hàng</th>


                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Sản phẩm</th>
                    <th>Người nhận</th>
                    <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $statusLabel = [
                        'pending'   => ['Chờ XN',     'badge-warning'],
                        'confirmed' => ['Đã XN',      'badge-info'],
                        'shipping'  => ['Đang giao',  'badge-info'],
                        'completed' => ['Hoàn thành', 'badge-success'],
                        'cancelled' => ['Đã hủy',     'badge-danger'],
                    ];
                @endphp
                @forelse($orders as $o)
                    <tr>
                        <td>{{ $o->id }}</td>
                        <td><strong>{{ $o->ma_don_hang }}</strong></td>
                        <td>
                            {{ $o->user?->name ?? $o->name }}
                            <br><small style="color:#8a9bb0">{{ $o->user?->tel }}</small>
                        </td>
                        <td>{{ number_format($o->total_price) }}đ</td>
                        <td>
                            @php $s = $statusLabel[$o->status] ?? [$o->status, 'badge-secondary']; @endphp
                            <span class="badge {{ $s[1] }}">{{ $s[0] }}</span>
                        </td>

                        @php
                            $tinh = $o->province?->name ?? '';
                            $quan = $o->district?->name ?? '';
                            $phuong = $o->ward?->name ?? '';

                            $dia_chi_ghi = $o->receive_address ?? '';

                            $dia_chi_noi = trim(implode(', ', array_filter([
                                $dia_chi_ghi,
                                $phuong,
                                $quan,
                                $tinh
                            ])));
                        @endphp
                        <td>
                            @foreach($o->details->take(2) as $d)
                                <div>{{ $d->product?->name ?? '#'.$d->product_id }}</div>
                            @endforeach

                            @if($o->details->count() > 2)
                                <small style="color:#8a9bb0">
                                    +{{ $o->details->count() - 2 }} sản phẩm
                                </small>
                            @endif

                            {{--                                <p><strong>Địa chỉ:</strong> {{ $dia_chi_noi }}</p>--}}

                        </td>
                        <td>
                            {{ $o->receive_user ?? '—' }}
                            <br>
                            <p style="color:#8a9bb0">{{ $o->receive_phone }}</p>
                            <br>
                            <p style="color:#555">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $dia_chi_noi ?: 'Chưa có địa chỉ' }}
                            </p>

                        </td>
                        <td>{{ $o->created_at?->format('d/m/Y') }}</td>
                                                <td style="white-space:nowrap">
                                                    <a href="{{ route('admin.orders.show', $o) }}" class="btn btn-primary btn-sm"><i
                                                                class="fas fa-eye"></i></a>
{{--                                                    <a href="{{ route('admin.orders.edit', $o) }}" class="btn btn-warning btn-sm"><i--}}
{{--                                                                class="fas fa-edit"></i></a>--}}
{{--                                                    <form method="POST" action="{{ route('admin.orders.destroy', $o) }}" style="display:inline"--}}
{{--                                                          onsubmit="return confirm('Xóa?')">--}}
{{--                                                        @csrf @method('DELETE')--}}
{{--                                                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>--}}
{{--                                                    </form>--}}
                                                </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:#8a9bb0;padding:40px">Chưa có đơn hàng.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div style="padding:16px 20px">{{ $orders->links() }}</div>
        </div>
    </div>
@endsection
