@extends('Core::Admin.layouts.app')
@section('title', 'Thêm sản phẩm')
@section('page-title', 'Thêm sản phẩm mới')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Thêm sản phẩm</h5>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Tên sản phẩm <span style="color:red">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Danh mục</label>
                            <select name="category_id" class="form-control">
                                <option value="">— Chọn danh mục —</option>
                                @foreach($categories as $c)<option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Sản phẩm cha <small style="color:#8a9bb0">(tuỳ chọn)</small></label>
                            <select name="parent_id" class="form-control">
                                <option value="">— Không có cha —</option>
                                @foreach($products as $p)<option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Giá bán</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price') }}" min="0">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Giá gốc (cũ)</label>
                            <input type="number" name="old_price" class="form-control" value="{{ old('old_price') }}" min="0">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Hoàn tiền</label>
                            <input type="number" name="price_hoan_tien" class="form-control" value="{{ old('price_hoan_tien') }}" min="0">
                        </div>
                    </div>


                    <div class="col-4">
                        <div class="form-group">
                            <label>Số thứ tự hiện thị</label>
                            <input type="number" name="stt" class="form-control" value="{{ old('stt') }}" min="0">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>Mô tả ngắn</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Nội dung chi tiết</label>
                            <textarea name="content" class="form-control ckeditor" rows="6">{{ old('content') }}</textarea>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>Hướng dẫn và khuyến cáo</label>
                            <textarea name="instructions" class="form-control ckeditor" rows="6">{{ old('instructions') }}</textarea>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>Nguồn gốc</label>
                            <input type="text" name="origin" class="form-control" value="{{ old('origin') }}">
                        </div>
                    </div>

                    {{-- ====== ẢNH ====== --}}
                    <div class="col-12" style="margin-top:8px">
                        <h6 style="color:#4e8ef7;font-weight:700;padding-bottom:6px;border-bottom:2px solid #e9ecef;margin-bottom:14px"><i class="fas fa-image"></i> Hình ảnh</h6>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Ảnh đại diện (thumbnail)</label>
                            <input type="file" name="image" class="form-control" accept="image/*" id="main-img-input" onchange="previewMainImg(this)">
                            <div id="main-img-preview" style="margin-top:10px"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Ảnh phụ (gallery) <small style="color:#8a9bb0">Chọn nhiều ảnh</small></label>
                            <input type="file" name="image_extra[]" class="form-control" accept="image/*" multiple id="extra-img-input" onchange="previewExtraImgs(this)">
                            <div id="extra-preview" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px"></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>Giấy phép (list ảnh) <small style="color:#8a9bb0">Chọn nhiều ảnh</small></label>
                            <input type="file" name="licenses[]" class="form-control" accept="image/*" multiple id="licenses-input" onchange="previewLicenses(this)">
                            <div id="licenses-preview" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px"></div>
                        </div>
                    </div>

                    {{-- ====== VIDEO ====== --}}
                    <div class="col-12" style="margin-top:8px">
                        <h6 style="color:#e74c3c;font-weight:700;padding-bottom:6px;border-bottom:2px solid #e9ecef;margin-bottom:14px"><i class="fas fa-play-circle"></i> Video sản phẩm</h6>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>URL Video <small style="color:#8a9bb0">YouTube (https://www.youtube.com/watch?v=...) hoặc link nhúng trực tiếp</small></label>
                            <input type="text" name="video" class="form-control @error('video') is-invalid @enderror"
                                   value="{{ old('video') }}"
                                   placeholder="VD: https://www.youtube.com/watch?v=abcd1234"
                                   id="video-input" oninput="previewVideo(this.value)">
                            @error('video')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div id="video-preview" style="margin-top:12px"></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>Tags</label>
                            <select name="tags[]" class="form-control" multiple style="height:100px">
                                @foreach($tags as $tag)<option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>{{ $tag->name }}</option>@endforeach
                            </select>
                            <small style="color:#8a9bb0">Giữ Ctrl để chọn nhiều</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group" style="display:flex;gap:20px;flex-wrap:wrap">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="active" value="1" {{ old('active',1)?'checked':'' }}> Hiển thị</label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="hien_trang_chu" value="1" {{ old('hien_trang_chu')?'checked':'' }}> Hiện trang chủ</label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="san_pham_noi_bat" value="1" {{ old('san_pham_noi_bat')?'checked':'' }}> Nổi bật</label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="san_pham_hien_nhu_baner" value="1" {{ old('san_pham_hien_nhu_baner')?'checked':'' }}> Hiện banner</label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="san_pham_hoan" value="1" {{ old('san_pham_hoan')?'checked':'' }}> SP không được hoàn tiền hoàn tiền</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu lại</button>
            </form>
        </div>
    </div>
    @push('scripts')



        <script>
            function previewMainImg(input) {
                const preview = document.getElementById('main-img-preview');
                preview.innerHTML = '';
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        preview.innerHTML = '<img src="' + e.target.result +
                            '" style="width:90px;height:90px;object-fit:cover;border-radius:8px;border:2px solid #4e8ef7">';
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function previewExtraImgs(input) {
                const preview = document.getElementById('extra-preview');
                preview.innerHTML = '';
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style =
                            'width:80px;height:80px;object-fit:cover;border-radius:7px;border:1.5px solid #e2e8f0';
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            }

            function previewVideo(url) {
                const box = document.getElementById('video-preview');
                if (!url) {
                    box.innerHTML = '';
                    return;
                }
                const embedUrl = youtubeEmbed(url);
                if (embedUrl) {
                    box.innerHTML =
                        '<div style="position:relative;padding-bottom:40%;background:#000;border-radius:10px;overflow:hidden">' +
                        '<iframe src="' + embedUrl +
                        '" style="position:absolute;inset:0;width:100%;height:100%;border:0" allowfullscreen></iframe></div>';
                } else {
                    box.innerHTML = '<video src="' + url +
                        '" controls style="max-width:100%;border-radius:10px;max-height:220px"></video>';
                }
            }

            function previewLicenses(input) {
                const preview = document.getElementById('licenses-preview');
                preview.innerHTML = '';
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style =
                            'width:80px;height:80px;object-fit:cover;border-radius:7px;border:1.5px solid #e74c3c';
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            }

            function youtubeEmbed(url) {
                const m = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
                return m ? 'https://www.youtube.com/embed/' + m[1] : null;
            }
        </script>


        <script>
            document.addEventListener('DOMContentLoaded', function () {

                document.querySelectorAll('.ckeditor').forEach(function (element) {

                    CKEDITOR.replace(element, {
                        filebrowserUploadUrl: '{{ route('admin.products.upload-image') }}',
                        filebrowserUploadMethod: 'form'
                    });

                });

            });
        </script>



    @endpush
@endsection
