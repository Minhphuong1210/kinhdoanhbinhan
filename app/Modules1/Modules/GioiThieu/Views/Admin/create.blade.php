@extends('Core::Admin.layouts.app')
@section('title', isset($gioiThieu) ? 'Sửa Giới thiệu' : 'Thêm Giới thiệu')
@section('page-title', isset($gioiThieu) ? 'Sửa Giới thiệu' : 'Thêm Giới thiệu')
@section('content')
<div class="card" style="max-width:760px">
    <div class="card-header">
        <h5>{{ isset($gioiThieu) ? 'Sửa: '.$gioiThieu->name : 'Thêm mới' }}</h5>
        <a href="{{ route('admin.gioi-thieus.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($gioiThieu) ? route('admin.gioi-thieus.update', $gioiThieu) : route('admin.gioi-thieus.store') }}" enctype="multipart/form-data">
            @csrf @if(isset($gioiThieu)) @method('PUT') @endif

            <div class="form-group"><label>Tên <span style="color:red">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $gioiThieu->name ?? '') }}" required>
            </div>

            <div class="row">
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
                <div class="col-6">
                    <div class="row">
                        <div class="col-6"><div class="form-group"><label>Vị trí số (cũ)</label>
                            <input type="number" name="vi_tri" class="form-control" value="{{ old('vi_tri', $gioiThieu->vi_tri ?? '') }}" placeholder="1, 2, 3...">
                        </div></div>
                        <div class="col-6"><div class="form-group"><label>Năm (timeline)</label>
                            <input type="number" name="year" class="form-control" value="{{ old('year', $gioiThieu->year ?? '') }}" placeholder="2014">
                        </div></div>
                    </div>
                    <div class="form-group"><label>Vị trí web</label>
                        <select name="vi_tri_web" class="form-control">
                            <option value="">— Chọn —</option>
                            <option value="htx"     {{ old('vi_tri_web', $gioiThieu->vi_tri_web ?? env('VI_TRI_WEB')) == 'htx'     ? 'selected' : '' }}>HTX</option>
                            <option value="cong_ty" {{ old('vi_tri_web', $gioiThieu->vi_tri_web ?? env('VI_TRI_WEB')) == 'cong_ty' ? 'selected' : '' }}>Công ty</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group"><label>Ảnh</label>
                @if(isset($gioiThieu) && $gioiThieu->image)
                    <img src="{{ cdn($gioiThieu->image) }}" style="width:80px;border-radius:7px;margin-bottom:8px;display:block">
                @endif
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <div class="form-group"><label>Nội dung</label>
                <textarea name="content" class="form-control ckeditor" rows="6">{{ old('content', $gioiThieu->content ?? '') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ isset($gioiThieu) ? 'Cập nhật' : 'Lưu lại' }}</button>
        </form>
    </div>
</div>
@endsection
