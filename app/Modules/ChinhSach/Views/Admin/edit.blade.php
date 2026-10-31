@extends('Core::Admin.layouts.app')
@section('title', 'Sửa Chính sách')
@section('page-title', 'Sửa Chính sách')
@section('content')
<div class="card" style="max-width:700px">
    <div class="card-header"><h5>Sửa: {{ $chinhSach->title }}</h5><a href="{{ route('admin.chinh-sachs.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.chinh-sachs.update', $chinhSach) }}" enctype="multipart/form-data">@csrf @method('PUT')
            <div class="form-group"><label>Tiêu đề <span style="color:red">*</span></label><input type="text" name="title" class="form-control" value="{{ old('title', $chinhSach->title) }}" required></div>
            <div class="row">
                <div class="col-6"><div class="form-group"><label>URL</label><input type="text" name="url" class="form-control" value="{{ old('url', $chinhSach->url) }}"></div></div>
                <div class="col-6"><div class="form-group"><label>Vị trí web</label>
                    <select name="vi_tri_web" class="form-control">
                        <option value="">— Chọn vị trí —</option>
                        <option value="htx"     {{ old('vi_tri_web', $chinhSach->vi_tri_web) == 'htx'     ? 'selected' : '' }}>HTX</option>
                        <option value="cong_ty" {{ old('vi_tri_web', $chinhSach->vi_tri_web) == 'cong_ty' ? 'selected' : '' }}>Công ty</option>
                    </select>
                </div></div>
            </div>
            <div class="form-group"><label>Ảnh</label>@if($chinhSach->image)<img src="{{ cdn($chinhSach->image) }}" style="width:80px;border-radius:7px;margin-bottom:8px;display:block">@endif<input type="file" name="image" class="form-control" accept="image/*"></div>
            <div class="form-group"><label>Nội dung</label><textarea name="content" class="form-control ckeditor" rows="5">{{ old('content', $chinhSach->content) }}</textarea></div>
            <div class="form-group"><label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="status" value="1" {{ old('status', $chinhSach->status)?'checked':'' }}> Hiển thị</label></div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
        </form>
    </div>
</div>
@endsection
