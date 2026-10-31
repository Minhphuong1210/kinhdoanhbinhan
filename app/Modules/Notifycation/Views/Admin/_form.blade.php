{{-- Form dùng chung cho thêm / sửa thông báo chung. Biến: $targets, $item (null khi thêm) --}}
<div class="form-group">
    <label>Gửi tới <span style="color:red">*</span></label>
    <select name="vi_tri" class="form-control @error('vi_tri') is-invalid @enderror" required>
        @foreach($targets as $value => $label)
            <option value="{{ $value }}" {{ old('vi_tri', $item->vi_tri ?? 'tat_ca') === $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    @error('vi_tri')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-group">
    <label>Tiêu đề <span style="color:red">*</span></label>
    <input type="text" name="title" maxlength="255" class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title', $item->title ?? '') }}" required>
    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-group">
    <label>Nội dung <span style="color:red">*</span></label>
    {{-- Không đặt required: CKEditor ẩn textarea, trình duyệt sẽ chặn submit. Server đã validate. --}}
    <textarea name="content" rows="8" class="form-control ckeditor @error('content') is-invalid @enderror"
              data-upload-url="{{ route('admin.thong-bao.upload-image') }}">{{ old('content', $item->content ?? '') }}</textarea>
    @error('content')<div class="invalid-feedback" style="display:block">{{ $message }}</div>@enderror
    <small style="color:#8a9bb0">Chèn ảnh bằng nút ảnh trên thanh công cụ (JPG, PNG, WebP, GIF — tối đa 5MB).</small>
</div>

<div class="form-group">
    <label>Ảnh đại diện (không bắt buộc)</label>
    @if(!empty($item?->image))
        <img src="{{ cdn($item->image) }}" alt="" style="width:120px;border-radius:7px;margin-bottom:8px;display:block">
    @endif
    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <small style="color:#8a9bb0">Hiện ở danh sách thông báo và trong thông báo đẩy.</small>
</div>
