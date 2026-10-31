@extends('Core::Admin.layouts.app')
@section('title', 'Thêm cài đặt')
@section('page-title', 'Thêm cài đặt')
@section('content')
@php($oldType = old('type', 'text'))
<div class="card" style="max-width:600px">
    <div class="card-header"><h5>Thêm cài đặt</h5><a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.store') }}" enctype="multipart/form-data">@csrf
            <div class="form-group"><label>Tên (key) <span style="color:red">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="site_name, contact_email..."></div>
            <div class="form-group"><label>Loại</label>
                <select name="type" id="setting-type" class="form-control" onchange="toggleSettingValue()">
                    @foreach(['text', 'textarea', 'image', 'video', 'boolean'] as $type)
                        <option value="{{ $type }}" {{ $oldType === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" id="value-text"><label>Giá trị</label><textarea name="value" class="form-control" rows="3">{{ old('value') }}</textarea></div>
            <div class="form-group" id="value-file" style="display:none">
                <label id="value-file-label">File</label>
                <input type="file" name="file" id="setting-file" class="form-control @error('file') is-invalid @enderror">
                @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small id="value-file-help" style="color:#8a9bb0"></small>
            </div>
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
<script>
    function toggleSettingValue() {
        var type = document.getElementById('setting-type').value;
        var isFile = type === 'image' || type === 'video';
        var file = document.getElementById('setting-file');

        document.getElementById('value-text').style.display = isFile ? 'none' : '';
        document.getElementById('value-file').style.display = isFile ? '' : 'none';
        document.getElementById('value-file-label').textContent = type === 'video' ? 'Video' : 'Ảnh';
        file.accept = type === 'video' ? 'video/mp4,video/webm,video/ogg' : 'image/*';
        document.getElementById('value-file-help').textContent = type === 'video'
            ? 'MP4, WebM, OGG — tối đa 2MB'
            : 'JPG, PNG, WebP, GIF, SVG — tối đa 2MB';
    }
    toggleSettingValue();
</script>
@endsection
