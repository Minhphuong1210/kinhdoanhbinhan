@extends('Core::Admin.layouts.app')
@section('title', 'Thêm Lãnh đạo')
@section('page-title', 'Thêm Lãnh đạo')
@section('content')
<div class="card">
    <div class="card-header">
        <h5>Thêm Lãnh đạo mới</h5>
        <a href="{{ route('admin.leaders.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.leaders.store') }}" enctype="multipart/form-data">@csrf

            {{-- ======= THÔNG TIN CƠ BẢN ======= --}}
            <h6 style="color:#4e8ef7;font-weight:700;margin-bottom:14px;padding-bottom:8px;border-bottom:2px solid #e9ecef"><i class="fas fa-user-tie"></i> Thông tin cơ bản</h6>
            <div class="row">
                <div class="col-6"><div class="form-group">
                    <label>Họ tên <span style="color:red">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div></div>

                <div class="col-6"><div class="form-group">
                    <label>Chức vụ</label>
                    <input type="text" name="chucvu" class="form-control" value="{{ old('chucvu') }}" placeholder="VD: Chủ tịch HĐQT...">
                </div></div>

                <div class="col-4"><div class="form-group">
                    <label>Giới tính</label>
                    <select name="gender" class="form-control">
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div></div>

                <div class="col-4"><div class="form-group">
                    <label>Ngày sinh</label>
                    <input type="date" name="ngaysinh" class="form-control" value="{{ old('ngaysinh') }}">
                </div></div>

                <div class="col-4"><div class="form-group">
                    <label>Số điện thoại / Zalo</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div></div>

                <div class="col-4"><div class="form-group">
                    <label>Học vấn</label>
                    <input type="text" name="hoc_van" class="form-control" value="{{ old('hoc_van') }}" placeholder="VD: Thạc sĩ Kinh tế...">
                </div></div>

                <div class="col-4"><div class="form-group">
                    <label>Số năm kinh nghiệm</label>
                    <input type="text" name="years" class="form-control" value="{{ old('years') }}" placeholder="VD: 15 năm">
                </div></div>

                <div class="col-4"><div class="form-group">
                    <label>Thứ tự hiển thị</label>
                    <input type="number" name="order_no" class="form-control" value="{{ old('order_no', 0) }}" min="0">
                </div></div>

                <div class="col-12"><div class="form-group">
                    <label>Vai trò</label>
                    <div style="display:flex;gap:24px;flex-wrap:wrap;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:7px">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                            <input type="checkbox" name="ambassador" value="1" {{ old('ambassador') ? 'checked' : '' }}>
                            Lãnh đạo cống hiến
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                            <input type="checkbox" name="is_ambassador" value="1" {{ old('is_ambassador') ? 'checked' : '' }}>
                            Đại sứ cống hiến
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                            <input type="checkbox" name="status" value="1" {{ old('status', 1) ? 'checked' : '' }}>
                            Hiển thị
                        </label>
                    </div>
                </div></div>

                <div class="col-12"><div class="form-group">
                    <label>Ảnh đại diện</label>
                    <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImg(this,'img-preview')">
                    <div id="img-preview" style="margin-top:8px"></div>
                </div></div>
            </div>

            {{-- ======= NỘI DUNG CHUYÊN MÔN ======= --}}
            <h6 style="color:#4e8ef7;font-weight:700;margin:20px 0 14px;padding-bottom:8px;border-bottom:2px solid #e9ecef"><i class="fas fa-briefcase"></i> Chuyên môn & Sự nghiệp</h6>

            <div class="form-group">
                <label>Lĩnh vực chuyên môn & thế mạnh</label>
                <textarea name="advantages" class="form-control ckeditor" rows="5">{{ old('advantages') }}</textarea>
            </div>

            <div class="form-group">
                <label>Quá trình công tác</label>
                <textarea name="progress" class="form-control ckeditor" rows="5">{{ old('progress') }}</textarea>
            </div>

            {{-- ======= TRIẾT LÝ & CHÂM NGÔN ======= --}}
            <h6 style="color:#4e8ef7;font-weight:700;margin:20px 0 14px;padding-bottom:8px;border-bottom:2px solid #e9ecef"><i class="fas fa-quote-left"></i> Triết lý & Châm ngôn</h6>

            <div class="form-group">
                <label>Châm ngôn làm việc / Triết lí kinh doanh</label>
                <textarea name="intro" class="form-control ckeditor" rows="4">{{ old('intro') }}</textarea>
            </div>

            <div class="form-group">
                <label>Slogan / Câu nói đại diện</label>
                <input type="text" name="slogan" class="form-control" value="{{ old('slogan') }}" placeholder="Câu slogan ngắn gọn...">
            </div>

            {{-- ======= THÀNH TỰU & ĐÓNG GÓP ======= --}}
            <h6 style="color:#4e8ef7;font-weight:700;margin:20px 0 14px;padding-bottom:8px;border-bottom:2px solid #e9ecef"><i class="fas fa-trophy"></i> Thành tựu & Đóng góp</h6>

            <div class="form-group">
                <label>Thành tựu nổi bật</label>
                <textarea name="achievements" class="form-control ckeditor" rows="5">{{ old('achievements') }}</textarea>
            </div>

            <div class="form-group">
                <label>Những đóng góp lớn cho Kinh doanh Bình An</label>
                <textarea name="contributions" class="form-control ckeditor" rows="5">{{ old('contributions') }}</textarea>
            </div>

            <div class="form-group">
                <label>Mục tiêu và tầm nhìn cá nhân</label>
                <textarea name="goals" class="form-control ckeditor" rows="5">{{ old('goals') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu lại</button>
        </form>
    </div>
</div>
@push('scripts')
<script>
function previewImg(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = '<img src="'+e.target.result+'" style="max-height:160px;border-radius:8px;border:2px solid #4e8ef7;object-fit:cover">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
