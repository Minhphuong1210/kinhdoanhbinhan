@extends('Core::Admin.layouts.app')
@section('title', 'Sửa sản phẩm')
@section('page-title', 'Sửa sản phẩm')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Sửa: {{ $product->name }}</h5>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Tên sản phẩm <span style="color:red">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Danh mục</label>
                            <select name="category_id" class="form-control">
                                <option value="">— Chọn danh mục —</option>
                                @foreach($categories as $c)<option value="{{ $c->id }}" {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Sản phẩm cha <small style="color:#8a9bb0">(tuỳ chọn)</small></label>
                            <select name="parent_id" class="form-control">
                                <option value="">— Không có cha —</option>
                                @foreach($products as $p)<option value="{{ $p->id }}" {{ old('parent_id', $product->parent_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Giá bán</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}" min="0">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Số lượng</label>
                            <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $product->quantity) }}" min="0">
                            <small style="color:#8a9bb0">Để trống nếu không giới hạn. Nhập 0 để ẩn khỏi mua.</small>
                        </div>
                    </div>
                    @if(auth()->user()->is_admin)
                    <div class="col-4">
                        <div class="form-group">
                            <label>Giá nhập</label>
                            <input type="number" name="purchase_price" class="form-control" value="{{ old('purchase_price', $product->purchase_price) }}" min="0">
                        </div>
                    </div>
                    @endif
                    <div class="col-4">
                        <div class="form-group">
                            <label>Giá gốc (cũ)</label>
                            <input type="number" name="old_price" class="form-control" value="{{ old('old_price', $product->old_price) }}" min="0">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Hoàn tiền</label>
                            <input type="number" name="price_hoan_tien" class="form-control" value="{{ old('price_hoan_tien', $product->price_hoan_tien) }}" min="0">
                        </div>
                    </div>
                    @if(auth()->user()->is_admin)
                        <div class="col-4">
                            <div class="form-group">
                                <label>Cơ chế hoàn phí</label>
                                <select name="co_che_hoan_phi_id" class="form-control">
                                    <option value="">Dùng setting mặc định</option>
                                    @foreach($coCheHoanPhis as $coChe)
                                        <option value="{{ $coChe->id }}" {{ old('co_che_hoan_phi_id', $product->co_che_hoan_phi_id) == $coChe->id ? 'selected' : '' }}>
                                            {{ $coChe->name }} ({{ $coChe->tang_1 }}% / {{ $coChe->tang_2 }}% / {{ $coChe->tang_3 }}% / {{ $coChe->tang_4 }}% / {{ $coChe->tang_5 }}% / {{ $coChe->tang_6 }}%)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="col-4">
                        <div class="form-group">
                            <label>Số thứ tự hiện thị</label>
                            <input type="number" name="stt" class="form-control" value="{{ old('stt', $product->stt) }}" min="0">
                        </div>
                    </div>
                    {{-- DESCRIPTION + CONTENT --}}
                    <div class="col-12">
                        <div class="form-group">
                            <label>Mô tả ngắn</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Nội dung chi tiết</label>
                            <textarea name="content" class="form-control ckeditor" rows="6">{{ old('content', $product->content) }}</textarea>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>Hướng dẫn và khuyến cáo</label>
                            <textarea name="instructions" class="form-control ckeditor" rows="6">{{ old('instructions', $product->instructions) }}</textarea>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>Nguồn gốc</label>
                            <input type="text" name="origin" class="form-control" value="{{ old('origin', $product->origin) }}">
                        </div>
                    </div>

                    {{-- ====== ẢNH ====== --}}
                    <div class="col-12" style="margin-top:8px">
                        <h6 style="color:#4e8ef7;font-weight:700;padding-bottom:6px;border-bottom:2px solid #e9ecef;margin-bottom:14px"><i class="fas fa-image"></i> Hình ảnh</h6>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Ảnh đại diện (thumbnail)</label>
                            @if($product->image)<img src="{{ cdn($product->image) }}" style="width:80px;border-radius:7px;margin-bottom:8px;display:block" id="main-current-img">@endif
                            <input type="file" name="image" class="form-control" accept="image/*" id="main-img-input" onchange="previewMainImg(this)">
                            <div id="main-img-preview" style="margin-top:8px"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Ảnh phụ (gallery) - Tải lên thêm</label>
                            <input type="file" name="image_extra[]" class="form-control" accept="image/*" multiple onchange="previewExtraImgs(this)">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Giấy phép (list ảnh) - Tải lên thêm</label>
                            <input type="file" name="licenses[]" class="form-control" accept="image/*" multiple id="licenses-input" onchange="previewLicenses(this)">
                        </div>
                    </div>
                    {{-- Preview ảnh mới full-width --}}
                    <div class="col-12">
                        <div id="extra-preview" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:4px;margin-bottom:10px"></div>
                    </div>
                    <div class="col-12">
                        <div id="licenses-preview" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:4px;margin-bottom:10px"></div>
                    </div>
                    {{-- Ảnh phụ hiện có --}}
                    @if(!empty($extraImages))
                        <div class="col-12">
                            <div class="form-group">
                                <label>Ảnh gallery hiện có — tick để giữ, bỏ tick để xóa</label>
                                <div style="display:flex;flex-wrap:wrap;gap:12px;padding:14px;border:1.5 solid #e2e8f0;border-radius:8px;margin-top:6px">
                                    @foreach($extraImages as $img)
                                        <label style="cursor:pointer;text-align:center">
                                            <img src="{{ cdn($img)  }}" style="width:90px;height:90px;object-fit:cover;border-radius:7px;display:block;margin-bottom:6px;border:2px solid #22c55e" class="extra-img-keep">
                                            <input type="checkbox" name="keep_extra[]" value="{{ $img }}" checked onchange="toggleImgStyle(this)">
                                            <small style="font-size:11px;color:#8a9bb0;display:block">Giữ</small>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Giấy phép hiện có --}}
                    @if($product->licenses && count($product->licenses) > 0)
                        <div class="col-12">
                            <div class="form-group">
                                <label>Giấy phép hiện có — tick để giữ, bỏ tick để xóa</label>
                                <div style="display:flex;flex-wrap:wrap;gap:12px;padding:14px;border:1.5px solid #fed7d7;border-radius:8px;margin-top:6px">
                                    @foreach($product->licenses as $img)
                                        <label style="cursor:pointer;text-align:center">
                                            <img src="{{ cdn($img)  }}" style="width:90px;height:90px;object-fit:cover;border-radius:7px;display:block;margin-bottom:6px;border:2px solid #22c55e" class="extra-img-keep">
                                            <input type="checkbox" name="keep_licenses[]" value="{{ $img }}" checked onchange="toggleImgStyle(this)">
                                            <small style="font-size:11px;color:#8a9bb0;display:block">Giữ</small>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ====== VIDEO ====== --}}
                    <div class="col-12" style="margin-top:8px">
                        <h6 style="color:#e74c3c;font-weight:700;padding-bottom:6px;border-bottom:2px solid #e9ecef;margin-bottom:14px"><i class="fas fa-play-circle"></i> Video sản phẩm</h6>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>URL Video <small style="color:#8a9bb0">YouTube (https://www.youtube.com/watch?v=...) hoặc link nhúng trực tiếp</small></label>
                            <input type="text" name="video" class="form-control @error('video') is-invalid @enderror"
                                   value="{{ old('video', $product->video) }}"
                                   placeholder="VD: https://www.youtube.com/watch?v=abcd1234"
                                   id="video-input" oninput="previewVideo(this.value)">
                            @error('video')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div id="video-preview" style="margin-top:12px">
                                @if($product->video)
                                    @php
                                        preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $product->video, $ytm);
                                        $ytId = $ytm[1] ?? null;
                                    @endphp
                                    @if($ytId)
                                        <div style="position:relative;padding-bottom:40%;background:#000;border-radius:10px;overflow:hidden">
                                            <iframe src="https://www.youtube.com/embed/{{ $ytId }}"
                                                    style="position:absolute;inset:0;width:100%;height:100%;border:0" allowfullscreen></iframe>
                                        </div>
                                    @else
                                        <video src="{{ $product->video }}" controls style="max-width:100%;border-radius:10px;max-height:220px"></video>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>Tags</label>
                            <select name="tags[]" class="form-control" multiple style="height:100px">
                                @foreach($tags as $tag)<option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $product->tags->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $tag->name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group" style="display:flex;gap:20px;flex-wrap:wrap">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="active" value="1" {{ old('active',$product->active)?'checked':'' }}> Hiển thị</label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="hien_trang_chu" value="1" {{ old('hien_trang_chu',$product->hien_trang_chu)?'checked':'' }}> Hiện trang chủ</label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="san_pham_noi_bat" value="1" {{ old('san_pham_noi_bat',$product->san_pham_noi_bat)?'checked':'' }}> Nổi bật</label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="san_pham_hien_nhu_baner" value="1" {{ old('san_pham_hien_nhu_baner',$product->san_pham_hien_nhu_baner)?'checked':'' }}> Hiện banner</label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="san_pham_hoan" value="1" {{ old('san_pham_hoan',$product->san_pham_hoan)?'checked':'' }}> SP không được hoàn tiền hoàn tiền</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
            </form>
        </div>
    </div>
    @push('scripts')

        <script>
            $(document).ready(function() {

                $('.ckeditor').each(function() {

                    CKEDITOR.replace(this, {
                        filebrowserUploadUrl: '{{ route('admin.products.upload-image') }}',
                        filebrowserUploadMethod: 'form'
                    });

                });

            });
        </script>

        <script>
            function previewMainImg(input) {
                const preview = document.getElementById('main-img-preview');
                preview.innerHTML = '';
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => { preview.innerHTML = '<img src="'+e.target.result+'" style="width:90px;height:90px;object-fit:cover;border-radius:8px;border:2px solid #4e8ef7">'; };
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
                        img.style = 'width:80px;height:80px;object-fit:cover;border-radius:7px;border:1.5px solid #4e8ef7';
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            }
            function toggleImgStyle(cb) {
                const img = cb.closest('label').querySelector('img.extra-img-keep');
                img.style.border = cb.checked ? '2px solid #22c55e' : '2px solid #ef4444';
                img.style.opacity = cb.checked ? '1' : '0.4';
            }
            function previewVideo(url) {
                const box = document.getElementById('video-preview');
                if (!url) { box.innerHTML = ''; return; }
                const embedUrl = youtubeEmbed(url);
                if (embedUrl) {
                    box.innerHTML = '<div style="position:relative;padding-bottom:40%;background:#000;border-radius:10px;overflow:hidden">'
                        + '<iframe src="'+embedUrl+'" style="position:absolute;inset:0;width:100%;height:100%;border:0" allowfullscreen></iframe></div>';
                } else {
                    box.innerHTML = '<video src="'+url+'" controls style="max-width:100%;border-radius:10px;max-height:220px"></video>';
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
                        img.style = 'width:80px;height:80px;object-fit:cover;border-radius:7px;border:1.5px solid #e74c3c';
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
    @endpush
@endsection
