@extends('Core::Admin.layouts.app')
@section('title', isset($widget) ? 'Sửa Widget' : 'Thêm Widget')
@section('page-title', isset($widget) ? 'Sửa Widget' : 'Thêm Widget')
@section('content')
<div class="card" style="max-width:700px">
    <div class="card-header"><h5>{{ isset($widget) ? 'Sửa: '.$widget->title : 'Thêm Widget' }}</h5><a href="{{ route('admin.widgets.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a></div>
    <div class="card-body">
        <form method="POST" action="{{ isset($widget) ? route('admin.widgets.update', $widget) : route('admin.widgets.store') }}" enctype="multipart/form-data">
            @csrf @if(isset($widget)) @method('PUT') @endif
            <div class="row">
                <div class="col-6"><div class="form-group"><label>Vị trí (location) <span style="color:red">*</span></label><input type="text" name="location" class="form-control" value="{{ old('location', $widget->location ?? '') }}" required placeholder="homepage-banner"></div></div>
                <div class="col-6"><div class="form-group"><label>Tiêu đề</label><input type="text" name="title" class="form-control" value="{{ old('title', $widget->title ?? '') }}"></div></div>
                <div class="col-6"><div class="form-group"><label>Tên (name)</label><input type="text" name="name" class="form-control" value="{{ old('name', $widget->name ?? '') }}"></div></div>
                <div class="col-6"><div class="form-group"><label>Thứ tự</label><input type="number" name="order_no" class="form-control" value="{{ old('order_no', $widget->order_no ?? 0) }}"></div></div>
                <div class="col-12"><div class="form-group"><label>Link</label><input type="text" name="link" class="form-control" value="{{ old('link', $widget->link ?? '') }}"></div></div>
                <div class="col-12"><div class="form-group"><label>Ảnh</label>@if(isset($widget) && $widget->image)<img src="{{ asset('storage/'.$widget->image) }}" style="width:80px;border-radius:7px;margin-bottom:8px;display:block">@endif<input type="file" name="image" class="form-control" accept="image/*"></div></div>
                <div class="col-12"><div class="form-group"><label>Nội dung</label><textarea name="content" class="form-control" rows="4">{{ old('content', $widget->content ?? '') }}</textarea></div></div>
                <div class="col-12"><div class="form-group"><label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="status" value="1" {{ old('status', $widget->status ?? 1)?'checked':'' }}> Hiển thị</label></div></div>
                <div class="col-6"><div class="form-group"><label>Vị trí web</label>
                        <select name="vi_tri_web" class="form-control">
                            <option value="">-- Cả 2 web --</option>
                            <option value="htx"     {{ old('vi_tri_web', $widget->vi_tri_web ?? env('VI_TRI_WEB')) == 'htx'     ? 'selected' : '' }}>HTX</option>
                            <option value="cong_ty" {{ old('vi_tri_web', $widget->vi_tri_web ?? env('VI_TRI_WEB')) == 'cong_ty' ? 'selected' : '' }}>Công ty</option>
                        </select>
                    </div></div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ isset($widget) ? 'Cập nhật' : 'Lưu lại' }}</button>
        </form>
    </div>
</div>
@endsection
