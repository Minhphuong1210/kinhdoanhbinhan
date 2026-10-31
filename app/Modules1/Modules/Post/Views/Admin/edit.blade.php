@extends('Core::Admin.layouts.app')
@section('title', 'Sửa bài viết')
@section('page-title', 'Sửa bài viết')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Sửa: {{ $post->name }}</h5>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary btn-sm"><i
                        class="fas fa-arrow-left"></i> Quay lại</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.posts.update', $post) }}"
                  enctype="multipart/form-data">@csrf @method('PUT')
                <div class="row">
                    <div class="col-6">
                        <div class="form-group"><label>Tiêu đề <span style="color:red">*</span></label><input
                                    type="text" name="name" class="form-control" value="{{ old('name', $post->name) }}"
                                    required></div>
                    </div>
                    <div class="col-6">
                        <div class="form-group"><label>Danh mục</label><select name="category_post_id"
                                                                               class="form-control">
                                <option value="">— Chọn —</option>@foreach($categories as $c)
                                    <option value="{{ $c->id }}" {{ old('category_post_id', $post->category_post_id) == $c->id ? 'selected':'' }}>{{ $c->name }}</option>
                                @endforeach</select></div>
                    </div>
                    <div class="col-6">
                        <div class="form-group"><label>Trạng thái</label><select name="status" class="form-control">
                                <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected':'' }}>
                                    Nháp
                                </option>
                                <option value="published" {{ old('status', $post->status) === 'published' ? 'selected':'' }}>
                                    Đã đăng
                                </option>
                            </select></div>
                    </div>


                    <div class="col-6">
                        <div class="form-group"><label>Vị trí web</label><select name="vi_tri_web" class="form-control">
                                <option value="htx">Hợp tác xã</option>
                                <option value="cong_ty">Công ty</option>
                            </select></div>
                    </div>

                    <div class="col-6">
                        <div class="form-group"><label>Ảnh đại diện</label>@if($post->image)
                                <img src="{{ cdn($post->image)  }}"
                                     style="width:60px;border-radius:6px;margin-bottom:6px;display:block">
                            @endif<input type="file" name="image" class="form-control" accept="image/*"></div>
                    </div>
                    <div class="col-12">
                        <div class="form-group"><label>Mô tả ngắn</label><textarea name="description"
                                                                                   class="form-control"
                                                                                   rows="3">{{ old('description', $post->description) }}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group"><label>Nội dung</label><textarea name="content"
                                                                                 class="form-control ckeditor"
                                                                                 rows="8">{{ old('content', $post->content) }}</textarea>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group"><label>Meta Title</label><input type="text" name="meta_title"
                                                                                class="form-control"
                                                                                value="{{ old('meta_title', $post->meta_title) }}">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group"><label>Meta Keywords</label><input type="text" name="meta_keywords"
                                                                                   class="form-control"
                                                                                   value="{{ old('meta_keywords', $post->meta_keywords) }}">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group"><label>Meta Description</label><input type="text"
                                                                                      name="meta_description"
                                                                                      class="form-control"
                                                                                      value="{{ old('meta_description', $post->meta_description) }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group"><label
                                    style="display:flex;align-items:center;gap:8px;cursor:pointer"><input
                                        type="checkbox" name="featured"
                                        value="1" {{ old('featured', $post->featured) ? 'checked':'' }}> Nổi bật</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
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
