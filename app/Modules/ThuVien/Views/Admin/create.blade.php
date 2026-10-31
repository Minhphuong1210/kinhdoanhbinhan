@extends('Core::Admin.layouts.app')
@section('title', isset($thuVien) ? 'Sửa Thư viện' : 'Thêm Thư viện')
@section('page-title', isset($thuVien) ? 'Sửa Thư viện' : 'Thêm Thư viện')
@section('content')
<div class="card" style="max-width:700px">
    <div class="card-header"><h5>{{ isset($thuVien) ? 'Sửa: '.$thuVien->name : 'Thêm Thư viện' }}</h5><a href="{{ route('admin.thu-viens.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a></div>
    <div class="card-body">
        <form method="POST" action="{{ isset($thuVien) ? route('admin.thu-viens.update', $thuVien) : route('admin.thu-viens.store') }}" enctype="multipart/form-data">
            @csrf @if(isset($thuVien)) @method('PUT') @endif
            <div class="form-group"><label>Tên <span style="color:red">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name', $thuVien->name ?? '') }}" required></div>
            <div class="form-group"><label>Ảnh</label>@if(isset($thuVien) && $thuVien->image)<img src="{{ asset('storage/'.$thuVien->image) }}" style="width:80px;border-radius:7px;margin-bottom:8px;display:block">@endif<input type="file" name="image" class="form-control" accept="image/*"></div>
            <div class="form-group"><label>Giới thiệu ngắn</label><textarea name="intro" class="form-control" rows="2">{{ old('intro', $thuVien->intro ?? '') }}</textarea></div>
            <div class="form-group"><label>Nội dung</label><textarea name="content" class="form-control ckeditor" rows="6">{{ old('content', $thuVien->content ?? '') }}</textarea></div>
            <div class="row">
                <div class="col-4"><div class="form-group"><label>Meta Title</label><input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $thuVien->meta_title ?? '') }}"></div></div>
                <div class="col-4"><div class="form-group"><label>Meta Keywords</label><input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $thuVien->meta_keywords ?? '') }}"></div></div>
                <div class="col-4"><div class="form-group"><label>Meta Desc</label><input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $thuVien->meta_description ?? '') }}"></div></div>
            </div>
            <div class="form-group"><label>Vị trí web</label>
                <select name="vi_tri_web" class="form-control">
                    <option value="">— Chọn vị trí —</option>
                    <option value="htx"     {{ old('vi_tri_web', $thuVien->vi_tri_web ?? env('VI_TRI_WEB')) == 'htx'     ? 'selected' : '' }}>HTX</option>
                    <option value="cong_ty" {{ old('vi_tri_web', $thuVien->vi_tri_web ?? env('VI_TRI_WEB')) == 'cong_ty' ? 'selected' : '' }}>Công ty</option>
                </select>
            </div>
            <div class="form-group" style="display:flex;gap:20px"><label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="status" value="1" {{ old('status', $thuVien->status ?? 1)?'checked':'' }}> Hiển thị</label><label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="featured" value="1" {{ old('featured', $thuVien->featured ?? 0)?'checked':'' }}> Nổi bật</label></div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ isset($thuVien) ? 'Cập nhật' : 'Lưu lại' }}</button>
        </form>
    </div>
</div>
@endsection
