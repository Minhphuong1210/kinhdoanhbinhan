@extends('Core::Admin.layouts.app')
@section('title', 'Sửa danh mục')
@section('page-title', 'Sửa danh mục')
@section('content')
<div class="card" style="max-width:700px">
    <div class="card-header">
        <h5>Sửa: {{ $category->name }}</h5>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Tên danh mục <span style="color:red">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Danh mục cha</label>
                <select name="parent_id" class="form-control">
                    <option value="">— Không có —</option>
                    @foreach($parents as $p)<option value="{{ $p->id }}" {{ old('parent_id', $category->parent_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>@endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Ảnh đại diện</label>
                @if($category->image)<img src="{{ cdn($category->image)}}" style="width:80px;border-radius:7px;margin-bottom:8px;display:block" alt="">@endif
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                    <input type="checkbox" name="active" value="1" {{ old('active', $category->active) ? 'checked' : '' }}> Hiển thị
                </label>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
        </form>
    </div>
</div>
@endsection
