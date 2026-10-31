@extends('Core::Admin.layouts.app')
@section('title', isset($role) ? 'Sửa Role' : 'Thêm Role')
@section('page-title', isset($role) ? 'Sửa Role' : 'Thêm Role mới')
@section('content')
<div class="card" style="max-width:700px">
    <div class="card-header">
        <h5>{{ isset($role) ? 'Sửa: '.$role->name : 'Thêm Role' }}</h5>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
            @csrf @if(isset($role)) @method('PUT') @endif
            <div class="form-group"><label>Tên role <span style="color:red">*</span></label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name ?? '') }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label>Display Name</label><input type="text" name="display_name" class="form-control" value="{{ old('display_name', $role->display_name ?? '') }}"></div>
            <div class="form-group">
                <label>Permissions</label>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;max-height:300px;overflow-y:auto;border:1.5px solid #e2e8f0;border-radius:7px;padding:12px">
                    @foreach($permissions as $perm)
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px">
                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                            {{ in_array($perm->id, old('permissions', isset($role) ? $role->permissions->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                        {{ $perm->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ isset($role) ? 'Cập nhật' : 'Lưu lại' }}</button>
        </form>
    </div>
</div>
@endsection
