@extends('Core::Admin.layouts.app')
@section('title', 'Thêm danh mục bài viết')
@section('page-title', 'Thêm danh mục bài viết')
@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header">
        <h5>Thêm danh mục bài viết</h5>
        <a href="{{ route('admin.category-posts.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.category-posts.store') }}">@csrf
            <div class="form-group"><label>Tên danh mục <span style="color:red">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
            <div class="form-group"><label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="active" value="1" {{ old('active',1)?'checked':'' }}> Hiển thị</label></div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu</button>
        </form>
    </div>
</div>
@endsection
