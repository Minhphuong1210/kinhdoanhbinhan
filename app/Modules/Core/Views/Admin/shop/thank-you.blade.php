@extends('Core::Admin.layouts.app')
@section('title', 'Đặt hàng thành công')
@section('page-title', 'Thành công')
@push('styles')
    <style>
        .thank-you-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }
        .thank-you-card {
            background-color: #f0fdf4;
            border-radius: 20px;
            padding: 40px 30px;
            max-width: 450px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid #dcfce7;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            background-color: transparent;
            border: 5px solid #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .icon-circle i {
            font-size: 40px;
            color: #22c55e;
        }
        .ty-title {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 10px;
        }
        .ty-desc {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 25px;
        }
        .ty-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 25px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #22c55e;
        }
        .stat-value.dark {
            color: #374151;
        }
        .stat-label {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 4px;
        }
        .ty-total-box {
            background-color: #ffffff;
            border-radius: 50px;
            padding: 15px 30px;
            display: inline-block;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #f3f4f6;
        }
        .total-label {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .total-amount {
            font-size: 22px;
            font-weight: 800;
            color: #111827;
        }
        .ty-date {
            font-size: 13px;
            color: #9ca3af;
            margin-bottom: 20px;
        }
        .btn-home {
            display: inline-block;
            background-color: #22c55e;
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }
        .btn-home:hover {
            background-color: #16a34a;
            color: white;
            transform: translateY(-1px);
        }
    </style>
@endpush

@section('content')
<div class="thank-you-wrapper">
    <div class="thank-you-card">
        <div class="icon-circle">
            <i class="fas fa-check"></i>
        </div>
        <h2 class="ty-title">Đặt hàng thành công!</h2>
        <p class="ty-desc">
            Cảm ơn bạn đã đặt hàng. Đơn hàng <b>#{{ $order->ma_don_hang }}</b> của bạn đã được ghi nhận và sẽ được xử lý trong thời gian sớm nhất.
        </p>

        <div class="ty-stats">
            <div class="stat-item">
                <div class="stat-value">{{ $order->details ? $order->details->sum('quantity') : 1 }}</div>
                <div class="stat-label">Sản phẩm</div>
            </div>
            <div class="stat-item">
                <div class="stat-value dark">{{ $order->details ? $order->details->sum('quantity') : 1 }}</div>
                <div class="stat-label">Tổng cộng</div>
            </div>
        </div>

        <div class="ty-total-box">
            <div class="total-label">Tổng thanh toán</div>
            <div class="total-amount">{{ number_format($order->total_price, 0, ',', '.') }} đ</div>
        </div>

        <div class="ty-date">
            {{ $order->created_at->format('d/m/Y H:i:s') }}
        </div>

        <div style="margin-top: 10px;">
            <a href="{{ route('admin.shop.index') }}" class="btn-home">Tiếp tục mua sắm</a>
        </div>
    </div>
</div>
@endsection
