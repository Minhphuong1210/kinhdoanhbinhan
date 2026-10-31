@extends('Core::Admin.layouts.app')
@section('title', 'Sửa Giới thiệu')
@section('page-title', 'Sửa Giới thiệu')
@section('content')
<div class="card" style="max-width:700px">
    <div class="card-header">
        <h5>Sửa: {{ $gioiThieu->name }}</h5>
        <a href="{{ route('admin.gioi-thieus.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.gioi-thieus.update', $gioiThieu) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group"><label>Tên <span style="color:red">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name', $gioiThieu->name) }}" required></div>
            <div class="row">
                <div class="col-6"><div class="form-group"><label>Vị trí số</label><input type="number" name="vi_tri" class="form-control" value="{{ old('vi_tri', $gioiThieu->vi_tri) }}"></div></div>
                <div class="col-6"><div class="form-group"><label>Năm</label><input type="number" name="year" class="form-control" value="{{ old('year', $gioiThieu->year) }}"></div></div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label>Vị trí nội dung (key)</label>
                    <select name="vi_tri_text" class="form-control">
                        <option value="">— Chọn vị trí —</option>
                        @php
                            $viTriOptions = [
                                've_chung_toi' => '📌 Giới thiệu chính (trang chủ — About Us)',
                                'gioi_thieu_1' => 'Câu chuyện 1',
                                'gioi_thieu_2' => 'Câu chuyện 2',
                                'gioi_thieu_3' => 'Câu chuyện 3',
                                'gioi_thieu_4' => 'Câu chuyện 4',
                            ];
                            $current = old('vi_tri_text', $gioiThieu->vi_tri ?? '');
                        @endphp
                        @foreach($viTriOptions as $val => $label)
                            <option value="{{ $val }}" {{ $current == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <small style="color:#8a9bb0;font-size:11.5px;margin-top:4px;display:block">
                        <i class="fas fa-info-circle"></i>
                        <b>gioi_thieu_5</b> = nội dung chính về chúng tôi trên trang chủ.<br>
                        Các key còn lại = hiển thị theo timeline câu chuyện.
                    </small>
                </div>
            </div>

            <div class="form-group"><label>Vị trí web</label><input type="text" name="vi_tri_web" class="form-control" value="{{ old('vi_tri_web', $gioiThieu->vi_tri_web) }}"></div>
            <div class="row">
                <div class="col-4"><div class="form-group"><label>Năm kinh doanh</label><input type="text" name="nam_kinh_doanh" class="form-control" maxlength="50" value="{{ old('nam_kinh_doanh', $gioiThieu->nam_kinh_doanh) }}" placeholder="10+"></div></div>
                <div class="col-4"><div class="form-group"><label>Khách hàng</label><input type="text" name="khach_hang" class="form-control" maxlength="50" value="{{ old('khach_hang', $gioiThieu->khach_hang) }}" placeholder="5.000+"></div></div>
                <div class="col-4"><div class="form-group"><label>Sản phẩm</label><input type="text" name="san_pham" class="form-control" maxlength="50" value="{{ old('san_pham', $gioiThieu->san_pham) }}" placeholder="200+"></div></div>
            </div>
            <div class="form-group"><label>Ảnh</label>@if($gioiThieu->image)<img src="{{ cdn($gioiThieu->image)  }}" style="width:80px;border-radius:7px;margin-bottom:8px;display:block">@endif<input type="file" name="image" class="form-control" accept="image/*"></div>
            <div class="form-group"><label>Nội dung</label><textarea name="content" class="form-control ckeditor" rows="5">{{ old('content', $gioiThieu->content) }}</textarea></div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
        </form>
    </div>
</div>
@endsection
