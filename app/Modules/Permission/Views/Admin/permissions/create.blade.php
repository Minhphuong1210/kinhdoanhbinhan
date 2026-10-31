{{-- Shared create/edit form for permission --}}
@extends('Core::Admin.layouts.app')
@section('title', isset($permission) ? 'Sửa Permission' : 'Thêm Permission')
@section('page-title', isset($permission) ? 'Sửa Permission' : 'Thêm Permission mới')
@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header">
        <h5>{{ isset($permission) ? 'Sửa: '.$permission->name : 'Thêm Permission' }}</h5>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($permission) ? route('admin.permissions.update', $permission) : route('admin.permissions.store') }}">
            @csrf @if(isset($permission)) @method('PUT') @endif
            <div class="form-group"><label>Tên (slug) <span style="color:red">*</span></label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $permission->name ?? '') }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror<small style="color:#8a9bb0">Ví dụ: product.create, order.view</small></div>
            <div class="form-group"><label>Display Name</label><input type="text" name="display_name" class="form-control" value="{{ old('display_name', $permission->display_name ?? '') }}" placeholder="VD: Xem sản phẩm"></div>
            <div class="form-group"><label>Mô tả <small style="color:#8a9bb0">(tuỳ chọn)</small></label><input type="text" name="description" class="form-control" value="{{ old('description', $permission->description ?? '') }}" placeholder="Ghi chú thêm về quyền này..."></div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ isset($permission) ? 'Cập nhật' : 'Lưu lại' }}</button>
        </form>
    </div>
</div>
@endsection
