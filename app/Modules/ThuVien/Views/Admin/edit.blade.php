@extends('Core::Admin.layouts.app')
@section('title', 'Sửa Thư viện')
@section('page-title', 'Sửa Thư viện')
@section('content')
<div class="card" style="max-width:700px">
    <div class="card-header"><h5>Sửa: {{ $thuVien->name }}</h5><a href="{{ route('admin.thu-viens.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.thu-viens.update', $thuVien) }}" enctype="multipart/form-data">@csrf @method('PUT')
            <div class="form-group"><label>Tên</label><input type="text" name="name" class="form-control" value="{{ old('name', $thuVien->name) }}" required></div>
            <div class="form-group"><label>Ảnh</label>@if($thuVien->image)<img src="{{ cdn($thuVien->image)  }}" style="width:80px;border-radius:7px;margin-bottom:8px;display:block">@endif<input type="file" name="image" class="form-control" accept="image/*"></div>
            <div class="form-group"><label>Giới thiệu ngắn</label><textarea name="intro" class="form-control" rows="2">{{ old('intro', $thuVien->intro) }}</textarea></div>
            <div class="form-group"><label>Nội dung</label><textarea name="content" class="form-control ckeditor" rows="6">{{ old('content', $thuVien->content) }}</textarea></div>
            <div class="row">
                <div class="col-4"><label>Meta Title</label><input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $thuVien->meta_title) }}"></div>
                <div class="col-4"><label>Meta Keywords</label><input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $thuVien->meta_keywords) }}"></div>
                <div class="col-4"><label>Meta Desc</label><input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $thuVien->meta_description) }}"></div>
            </div>
            <div class="form-group" style="display:flex;gap:20px;margin-top:16px"><label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="status" value="1" {{ old('status',$thuVien->status)?'checked':'' }}> Hiển thị</label><label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="featured" value="1" {{ old('featured',$thuVien->featured)?'checked':'' }}> Nổi bật</label></div>
            <button type="submit" class="btn btn-primary" style="margin-top:16px"><i class="fas fa-save"></i> Cập nhật</button>
        </form>
    </div>
</div>
@endsection
