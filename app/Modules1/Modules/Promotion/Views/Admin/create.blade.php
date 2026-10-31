@extends('Core::Admin.layouts.app')
@section('title', 'Tạo sự kiện Khuyến mãi')
@section('page-title', 'Tạo sự kiện mới')

@push('styles')
<style>
    .form-card{background:#fff;border-radius:12px;border:1.5px solid #e2e8f0;padding:28px;max-width:600px}
    .form-card h3{font-size:16px;font-weight:700;color:#1e2a3a;margin:0 0 20px;display:flex;align-items:center;gap:8px}
    .fg{margin-bottom:16px}
    .fg label{display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px}
    .fg input,.fg select,.fg textarea{width:100%;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;font-family:inherit;color:#1e2a3a;outline:none;transition:.2s}
    .fg input:focus,.fg select:focus,.fg textarea:focus{border-color:#4e8ef7;box-shadow:0 0 0 3px rgba(78,142,247,.1)}
    .fg .row2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .fg .help{font-size:11px;color:#9ca3af;margin-top:4px}
    .chk{display:flex;align-items:center;gap:8px;margin-bottom:20px}
    .chk input{width:18px;height:18px;accent-color:#4e8ef7}
    .chk label{font-size:13px;color:#374151;font-weight:500}
    .btn-save{padding:11px 28px;background:linear-gradient(135deg,#4e8ef7,#6c63ff);color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s}
    .btn-save:hover{opacity:.9;transform:translateY(-1px)}
    .btn-back{display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#64748b;text-decoration:none;margin-bottom:16px}
    .btn-back:hover{color:#4e8ef7}
    .err-msg{font-size:11px;color:#ef4444;margin-top:4px}
</style>
@endpush

@section('content')
    <a href="{{ route('admin.promotions.index') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Quay lại danh sách
    </a>

    <div class="form-card">
        <h3><i class="fas fa-gift" style="color:#6c63ff"></i> Tạo sự kiện Khuyến mãi</h3>

        <form action="{{ route('admin.promotions.store') }}" method="POST">
            @csrf

            <div class="fg">
                <label>Tên sự kiện <span style="color:red">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="VD: Khuyến mãi tháng 7/2026" required>
                @error('name')<div class="err-msg">{{ $message }}</div>@enderror
            </div>

            <div class="fg">
                <div class="row2">
                    <div>
                        <label>Tháng <span style="color:red">*</span></label>
                        <select name="month" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ old('month', now()->month) == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label>Năm <span style="color:red">*</span></label>
                        <input type="number" name="year" value="{{ old('year', now()->year) }}" min="2020" max="2099" required>
                    </div>
                </div>
            </div>

            <div class="fg">
                <label>Mô tả</label>
                <textarea name="description" rows="3" placeholder="Mô tả sự kiện (tuỳ chọn)">{{ old('description') }}</textarea>
            </div>

            <div class="chk">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                <label for="is_active">Kích hoạt ngay</label>
            </div>

            <button type="submit" class="btn-save">
                <i class="fas fa-check"></i> Tạo sự kiện
            </button>
        </form>
    </div>
@endsection
