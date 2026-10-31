@extends('Core::Admin.layouts.app')
@section('title', 'Cập nhật đơn hàng')
@section('page-title', 'Cập nhật trạng thái đơn hàng')
@section('content')
<div class="card" style="max-width:500px">
    <div class="card-header">
        <h5>Đơn: {{ $order->ma_don_hang }}</h5>
        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.orders.update', $order) }}">@csrf @method('PUT')
            <div class="form-group">
                <label>Trạng thái đơn hàng</label>
                <select name="status" class="form-control">
                    <option value="pending" {{ $order->status === 'pending' ? 'selected':'' }}>Chờ xác nhận</option>
                    <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected':'' }}>Đã xác nhận</option>
                    <option value="shipping" {{ $order->status === 'shipping' ? 'selected':'' }}>Đang giao hàng</option>
                    <option value="completed" {{ $order->status === 'completed' ? 'selected':'' }}>Hoàn thành</option>
                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected':'' }}>Đã hủy</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
        </form>
    </div>
</div>
@endsection
