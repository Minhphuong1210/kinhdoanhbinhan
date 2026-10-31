@extends('Core::Admin.layouts.app')
@section('title', 'Thêm Chính sách')
@section('page-title', 'Thêm Chính sách')
@section('content')
<div class="card" style="max-width:700px">
    <div class="card-header"><h5>Thêm Chính sách</h5><a href="{{ route('admin.chinh-sachs.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.chinh-sachs.store') }}" enctype="multipart/form-data">@csrf
            <div class="form-group"><label>Tiêu đề <span style="color:red">*</span></label><input type="text" name="title" class="form-control" value="{{ old('title') }}" required></div>
            <div class="row">
                <div class="col-6"><div class="form-group"><label>URL</label><input type="text" name="url" class="form-control" value="{{ old('url') }}"></div></div>
                <div class="col-6"><div class="form-group"><label>Vị trí web</label>
                    <select name="vi_tri_web" class="form-control">
                        <option value="">— Chọn vị trí —</option>
                        <option value="htx"     {{ old('vi_tri_web', env('VI_TRI_WEB')) == 'htx'     ? 'selected' : '' }}>HTX</option>
                        <option value="cong_ty" {{ old('vi_tri_web', env('VI_TRI_WEB')) == 'cong_ty' ? 'selected' : '' }}>Công ty</option>
                    </select>
                </div></div>
            </div>
            <div class="form-group"><label>Ảnh</label><input type="file" name="image" class="form-control" accept="image/*"></div>
            <div class="form-group"><label>Nội dung</label><textarea name="content" class="form-control ckeditor" rows="5">{{ old('content') }}</textarea></div>
            <div class="form-group"><label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="status" value="1" {{ old('status',1)?'checked':'' }}> Hiển thị</label></div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu lại</button>
        </form>
    </div>
</div>
@endsection
