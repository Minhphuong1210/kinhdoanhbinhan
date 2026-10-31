@extends('Core::Admin.layouts.app')
@section('title', 'Gửi thông báo')
@section('page-title', 'Gửi thông báo cho tất cả mọi người')
@section('content')
<div class="card" style="max-width:900px">
    <div class="card-header">
        <h5>Gửi thông báo</h5>
        <a href="{{ route('admin.thong-bao.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.thong-bao.store') }}" enctype="multipart/form-data"
              onsubmit="return confirm('Gửi thông báo này tới tất cả người dùng của app đã chọn? Đã gửi thì không thu hồi được trên điện thoại.')">
            @csrf
            @include('Notifycation::Admin._form', ['item' => null])
            <small style="color:#8a9bb0;display:block;margin-bottom:14px">
                <i class="fas fa-info-circle"></i> Thông báo đẩy tới mọi điện thoại đã đăng nhập app đã chọn (âm thanh mặc định, chỉ gửi phần chữ của nội dung) và hiện trong mục Thông báo của app.
            </small>
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Gửi thông báo</button>
        </form>
    </div>
</div>
@endsection
