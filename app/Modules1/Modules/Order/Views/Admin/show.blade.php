@extends('Core::Admin.layouts.app')
@section('title', 'Chi tiết đơn hàng')
@section('page-title', 'Chi tiết đơn hàng')
@section('content')
<div class="card">
    <div class="card-header">
        <h5>Đơn hàng: {{ $order->ma_don_hang }}</h5>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-6">

                @php

//                    $tinh = $order->province?->name ?? $order->province_id ?? '';
//                               $quan = $order->district?->name ?? $order->district_id ?? '';
//                               $phuong = $order->ward?->name ?? $order->ward_id ?? '';


 $tinh = $order->pancakeProvince?->name ?? $order->province_id ?? '';
    $quan = $order->pancakeDistrict?->name ?? $order->district_id ?? '';



    $phuong = $order->pancakeWard?->name ?? $order->ward_id ?? '';

                               $dia_chi_ghi = $order->receive_address ?? '';
//
            $dia_chi_noi = $dia_chi_ghi . ',' . $phuong . ',' . $quan . ',' . $tinh;

 @endphp

                <p><strong>Người nhận:</strong> {{ $order->receive_user }}</p>
                <p><strong>SĐT:</strong> {{ $order->receive_phone }}</p>
                <p><strong>Địa chỉ:</strong> {{ $dia_chi_noi }}</p>
                <p><strong>Ghi chú:</strong> {{ $order->note ?? '—' }}</p>
            </div>
            <div class="col-6">
                <p><strong>Tài khoản:</strong> {{ $order->user?->name }} ({{ $order->user?->email }})</p>
                <p><strong>Tổng tiền:</strong> {{ number_format($order->total_price) }}đ</p>
{{--                <p><strong>Hoàn tiền:</strong> {{ number_format($order->total_price_hoan_tien) }}đ</p>--}}
                <p><strong>Ngày đặt:</strong> {{ $order->created_at?->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        <hr>
        <h6 style="margin-bottom:14px;font-weight:600">Sản phẩm trong đơn</h6>
        <table>
            <thead><tr><th>Sản phẩm</th><th>Giá</th><th>SL</th><th>Thành tiền</th></tr></thead>
            <tbody>
            @foreach($order->details as $d)

                <tr class="{{ $d->the_loai == 'promotion' ? 'table-success' : '' }}">

                    <td>
                        {{ $d->product?->name ?? '#'.$d->product_id }}

                        @if($d->the_loai == 'promotion')
                            <span class="badge bg-success ms-2">
                🎁 Quà tặng khuyến mại
            </span>
                        @endif
                    </td>

                    <td>
                        @if($d->the_loai == 'promotion')
                            <span class="text-success fw-bold">Miễn phí</span>
                        @else
                            {{ number_format($d->price_product ?? 0) }}đ
                        @endif
                    </td>

                    <td>{{ $d->quantity ?? 1 }}</td>

                    <td>
                        @if($d->the_loai == 'promotion')
                            <span class="text-success fw-bold">0đ</span>
                        @else
                            {{ number_format(($d->price_product ?? 0) * ($d->quantity ?? 1)) }}đ
                        @endif
                    </td>

                </tr>

            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
