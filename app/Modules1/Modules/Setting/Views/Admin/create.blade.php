@extends('Core::Admin.layouts.app')
@section('title', 'Thêm cài đặt')
@section('page-title', 'Thêm cài đặt')
@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header"><h5>Thêm cài đặt</h5><a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.store') }}">@csrf
            <div class="form-group"><label>Tên (key) <span style="color:red">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="site_name, contact_email..."></div>
            <div class="form-group"><label>Giá trị</label><textarea name="value" class="form-control" rows="3">{{ old('value') }}</textarea></div>
            <div class="form-group"><label>Loại</label><select name="type" class="form-control"><option value="text">text</option><option value="textarea">textarea</option><option value="image">image</option><option value="boolean">boolean</option></select></div>
            <div class="form-group"><label>Nhóm (vị trí web)</label>
                <select name="vi_tri_web" class="form-control">
                    <option value="">— Chọn vị trí —</option>
                    <option value="htx"      {{ old('vi_tri_web', env('VI_TRI_WEB')) == 'htx'      ? 'selected' : '' }}>HTX</option>
                    <option value="cong_ty"  {{ old('vi_tri_web', env('VI_TRI_WEB')) == 'cong_ty'  ? 'selected' : '' }}>Công ty</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu lại</button>
        </form>
    </div>
</div>
@endsection
