@extends('Core::Admin.layouts.app')
@section('title', 'Thêm Tag')
@section('page-title', 'Thêm Tag mới')
@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header">
        <h5>Thêm Tag</h5>
        <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.tags.store') }}">@csrf
            <div class="form-group"><label>Tên tag <span style="color:red">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
            <div class="form-group"><label>Trạng thái</label><select name="status" class="form-control"><option value="active" selected>Hoạt động</option><option value="inactive">Ẩn</option></select></div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu</button>
        </form>
    </div>
</div>
@endsection
