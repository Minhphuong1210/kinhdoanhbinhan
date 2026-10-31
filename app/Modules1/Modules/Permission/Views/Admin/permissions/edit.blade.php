@extends('Core::Admin.layouts.app')
@section('title', 'Sửa Permission')
@section('page-title', 'Sửa Permission')
@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header">
        <h5>Sửa: {{ $permission->name }}</h5>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.permissions.update', $permission) }}">@csrf @method('PUT')
            <div class="form-group"><label>Tên (slug) <span style="color:red">*</span></label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $permission->name) }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label>Display Name</label><input type="text" name="display_name" class="form-control" value="{{ old('display_name', $permission->display_name) }}"></div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
        </form>
    </div>
</div>
@endsection
