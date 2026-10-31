@extends('Core::Admin.layouts.app')
@section('title', 'Sửa Tag')
@section('page-title', 'Sửa Tag')
@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header">
        <h5>Sửa: {{ $tag->name }}</h5>
        <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.tags.update', $tag) }}">@csrf @method('PUT')
            <div class="form-group"><label>Tên tag <span style="color:red">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name', $tag->name) }}" required></div>
            <div class="form-group"><label>Trạng thái</label><select name="status" class="form-control"><option value="active" {{ old('status', $tag->status) === 'active' ? 'selected':'' }}>Hoạt động</option><option value="inactive" {{ old('status', $tag->status) === 'inactive' ? 'selected':'' }}>Ẩn</option></select></div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
        </form>
    </div>
</div>
@endsection
