@extends('Core::Admin.layouts.app')
@section('title', 'Thêm bài viết')
@section('page-title', 'Thêm bài viết mới')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Thêm bài viết</h5>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary btn-sm"><i
                        class="fas fa-arrow-left"></i> Quay lại</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">@csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group"><label>Tiêu đề <span style="color:red">*</span></label><input
                                    type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" required>@error('name')
                            <div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    </div>
                    <div class="col-6">
                        <div class="form-group"><label>Danh mục</label><select name="category_post_id"
                                                                               class="form-control">
                                <option value="">— Chọn —</option>@foreach($categories as $c)
                                    <option value="{{ $c->id }}" {{ old('category_post_id') == $c->id ? 'selected':'' }}>{{ $c->name }}</option>
                                @endforeach</select></div>
                    </div>
                    <div class="col-6">
                        <div class="form-group"><label>Trạng thái</label><select name="status" class="form-control">
                                <option value="draft">Nháp</option>
                                <option value="published">Đã đăng</option>
                            </select></div>
                    </div>

                    <div class="col-6">
                        <div class="form-group"><label>Vị trí web</label><select name="vi_tri_web" class="form-control">
                                <option value="htx" {{ old('vi_tri_web', config('app.vi_tri_web', 'cong_ty')) === 'htx' ? 'selected' : '' }}>Hợp tác xã</option>
                                <option value="cong_ty" {{ old('vi_tri_web', config('app.vi_tri_web', 'cong_ty')) === 'cong_ty' ? 'selected' : '' }}>Công ty</option>
                            </select></div>
                    </div>

                    <div class="col-6">
                        <div class="form-group"><label>Ảnh đại diện</label><input type="file" name="image"
                                                                                  class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group"><label>Mô tả ngắn</label><textarea name="description"
                                                                                   class="form-control"
                                                                                   rows="3">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group"><label>Nội dung</label><textarea name="content"
                                                                                 class="form-control ckeditor"
                                                                                 rows="8">{{ old('content') }}</textarea>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group"><label>Meta Title</label><input type="text" name="meta_title"
                                                                                class="form-control"
                                                                                value="{{ old('meta_title') }}"></div>
                    </div>
                    <div class="col-4">
                        <div class="form-group"><label>Meta Keywords</label><input type="text" name="meta_keywords"
                                                                                   class="form-control"
                                                                                   value="{{ old('meta_keywords') }}">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group"><label>Meta Description</label><input type="text"
                                                                                      name="meta_description"
                                                                                      class="form-control"
                                                                                      value="{{ old('meta_description') }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group" style="display:flex;gap:20px"><label
                                    style="display:flex;align-items:center;gap:8px;cursor:pointer"><input
                                        type="checkbox" name="featured" value="1"> Nổi bật</label></div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu lại</button>
            </form>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.querySelectorAll('.ckeditor').forEach(function (element) {

                CKEDITOR.replace(element, {
                    filebrowserUploadUrl: '{{ route('admin.post.upload-image') }}',
                    filebrowserUploadMethod: 'form'
                });

            });

        });
    </script>

@endsection
