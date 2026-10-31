@extends('Core::Admin.layouts.app')
@section('title', 'Sửa thông báo')
@section('page-title', 'Sửa thông báo')
@section('content')
<div class="card" style="max-width:900px">
    <div class="card-header">
        <h5>Sửa thông báo #{{ $item->id }}</h5>
        <a href="{{ route('admin.thong-bao.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.thong-bao.update', $item) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('Notifycation::Admin._form', ['item' => $item])
            <small style="color:#8a9bb0;display:block;margin-bottom:14px">
                <i class="fas fa-info-circle"></i> Lưu lại chỉ cập nhật nội dung trong mục Thông báo của app. Thông báo đẩy đã gửi lúc {{ $item->created_at?->format('d/m/Y H:i') }} không gửi lại.
            </small>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu lại</button>
        </form>
    </div>
</div>
@endsection
