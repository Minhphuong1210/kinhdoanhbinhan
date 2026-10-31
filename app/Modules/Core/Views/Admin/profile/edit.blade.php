@extends('Core::Admin.layouts.app')

@section('title', 'Cập nhật thông tin địa chỉ')

@push('styles')
<style>
    /* ===== PAGE WRAPPER ===== */
    .profile-page {
        min-height: calc(100vh - 60px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px 16px;
        background: linear-gradient(135deg, #f0f4ff 0%, #f0f2f5 100%);
    }

    /* ===== CARD ===== */
    .profile-card {
        width: 100%;
        max-width: 560px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 40px rgba(0,0,0,.10);
        overflow: hidden;
    }

    /* ===== HEADER ===== */
    .profile-card-header {
        background: linear-gradient(135deg, #1a6b3c 0%, #2ecc71 100%);
        padding: 32px 36px 28px;
        text-align: center;
        position: relative;
    }
    .profile-card-header::after {
        content: '';
        position: absolute;
        bottom: -1px; left: 0; right: 0;
        height: 30px;
        background: #fff;
        border-radius: 50% 50% 0 0 / 30px 30px 0 0;
    }
    .profile-icon-wrap {
        width: 72px;
        height: 72px;
        background: rgba(255,255,255,.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
        font-size: 28px;
        color: #fff;
        border: 3px solid rgba(255,255,255,.35);
    }
    .profile-card-header h4 {
        color: #fff;
        font-size: 17px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .profile-card-header p {
        color: rgba(255,255,255,.8);
        font-size: 13px;
        margin: 0;
    }
    .profile-user-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,.2);
        color: #fff;
        font-size: 12.5px;
        font-weight: 600;
        padding: 4px 14px;
        border-radius: 20px;
        margin-top: 10px;
    }

    /* ===== BODY ===== */
    .profile-card-body {
        padding: 10px 36px 36px;
    }

    /* ===== ALERT ===== */
    .profile-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: #fff8e1;
        border: 1.5px solid #ffe082;
        border-radius: 9px;
        padding: 12px 16px;
        font-size: 13px;
        color: #7b5e00;
        margin-bottom: 22px;
        line-height: 1.5;
    }
    .profile-alert i { font-size: 15px; margin-top: 1px; flex-shrink: 0; color: #f59e0b; }

    /* ===== FORM FIELD ===== */
    .field-group {
        margin-bottom: 18px;
    }
    .field-group label {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 12.5px;
        font-weight: 600;
        color: #5a6a7e;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 7px;
    }
    .field-group label i { color: #1a6b3c; font-size: 13px; }
    .field-group label .req { color: #ef4444; margin-left: 1px; }
    .field-group select,
    .field-group input[type="text"] {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 9px;
        font-size: 13.5px;
        font-family: 'Segoe UI', sans-serif;
        background: #f8fafc;
        color: #1e2a3a;
        transition: border-color .2s, box-shadow .2s, background .2s;
        appearance: none;
        -webkit-appearance: none;
        outline: none;
    }
    .field-group select:focus,
    .field-group input[type="text"]:focus {
        border-color: #1a6b3c;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(26,107,60,.1);
    }
    .field-group select:disabled {
        background: #f1f3f7;
        color: #aab4be;
        cursor: not-allowed;
    }
    .field-group .field-error {
        color: #ef4444;
        font-size: 12px;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .field-group select.error,
    .field-group input.error {
        border-color: #ef4444;
        background: #fff5f5;
    }
    /* Select arrow icon */
    .select-wrap {
        position: relative;
    }
    .select-wrap::after {
        content: '\f107';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #8a9bb0;
        pointer-events: none;
        font-size: 13px;
    }
    .select-wrap select {
        padding-right: 36px;
    }

    /* ===== DIVIDER ===== */
    .field-divider {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 20px 0;
        color: #b0bec5;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .5px;
        text-transform: uppercase;
    }
    .field-divider::before, .field-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #f0f2f5;
    }

    /* ===== SUBMIT BTN ===== */
    .btn-profile-submit {
        width: 100%;
        padding: 13px;
        background: linear-gradient(135deg, #1a6b3c, #27ae60);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 14.5px;
        font-weight: 700;
        font-family: 'Segoe UI', sans-serif;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: opacity .2s, transform .15s, box-shadow .2s;
        box-shadow: 0 4px 16px rgba(26,107,60,.3);
        letter-spacing: .3px;
    }
    .btn-profile-submit:hover {
        opacity: .92;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(26,107,60,.35);
    }
    .btn-profile-submit:active { transform: translateY(0); }
    .btn-profile-submit i { font-size: 15px; }

    /* ===== LOADING STATE ===== */
    .btn-profile-submit.loading {
        pointer-events: none;
        opacity: .75;
    }

    /* ===== FOOTER NOTE ===== */
    .profile-note {
        text-align: center;
        margin-top: 18px;
        font-size: 12px;
        color: #b0bec5;
    }
    .profile-note i { margin-right: 4px; }
</style>
@endpush

@section('content')
<div class="profile-page">
    <div class="profile-card">

        {{-- HEADER --}}
        <div class="profile-card-header">
            <div class="profile-icon-wrap">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <h4>Cập nhật thông tin địa chỉ</h4>
            <p>Vui lòng xác nhận địa chỉ của bạn để tiếp tục sử dụng hệ thống</p>
            <div class="profile-user-badge">
                <i class="fas fa-user-circle"></i>
                {{ auth()->user()->name }}
            </div>
        </div>

        {{-- BODY --}}
        <div class="profile-card-body">

            {{-- Alert thông báo --}}
            @if(session('info'))
            <div class="profile-alert">
                <i class="fas fa-exclamation-triangle"></i>
                <span>{{ session('info') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom:18px;border-radius:9px;">
                <i class="fas fa-times-circle"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
            @endif

            <form action="{{ route('admin.profile.update') }}" method="POST" id="profileForm">
                @csrf

                {{-- Tỉnh/Thành phố --}}
                <div class="field-group">
                    <label>
                        <i class="fas fa-city"></i>
                        Tỉnh / Thành phố <span class="req">*</span>
                    </label>
                    <div class="select-wrap">
                        <select name="province_id" id="province_id"
                            class="{{ $errors->has('province_id') ? 'error' : '' }}">
                            <option value="">-- Chọn Tỉnh / Thành phố --</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}"
                                    {{ old('province_id', $user->province_id) == $province->id ? 'selected' : '' }}>
                                    {{ $province->type }} {{ $province->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('province_id')
                        <div class="field-error"><i class="fas fa-times-circle"></i>{{ $message }}</div>
                    @enderror
                </div>

                {{-- Quận/Huyện --}}
                <div class="field-group">
                    <label>
                        <i class="fas fa-map"></i>
                        Quận / Huyện <span class="req">*</span>
                    </label>
                    <div class="select-wrap">
                        <select name="district_id" id="district_id"
                            class="{{ $errors->has('district_id') ? 'error' : '' }}"
                            {{ $districts->isEmpty() ? 'disabled' : '' }}>
                            <option value="">-- Chọn Quận / Huyện --</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}"
                                    {{ old('district_id', $user->district_id) == $district->id ? 'selected' : '' }}>
                                    {{ $district->prefix }} {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('district_id')
                        <div class="field-error"><i class="fas fa-times-circle"></i>{{ $message }}</div>
                    @enderror
                </div>

                {{-- Xã/Phường --}}
                <div class="field-group">
                    <label>
                        <i class="fas fa-home"></i>
                        Xã / Phường / Thị trấn <span class="req">*</span>
                    </label>
                    <div class="select-wrap">
                        <select name="ward_id" id="ward_id"
                            class="{{ $errors->has('ward_id') ? 'error' : '' }}"
                            {{ $wards->isEmpty() ? 'disabled' : '' }}>
                            <option value="">-- Chọn Xã / Phường --</option>
                            @foreach($wards as $ward)
                                <option value="{{ $ward->id }}"
                                    {{ old('ward_id', $user->ward_id) == $ward->id ? 'selected' : '' }}>
                                    {{ $ward->prefix }} {{ $ward->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('ward_id')
                        <div class="field-error"><i class="fas fa-times-circle"></i>{{ $message }}</div>
                    @enderror
                </div>

                <div class="field-divider">Địa chỉ chi tiết</div>

                {{-- Địa chỉ cụ thể --}}
                <div class="field-group">
                    <label>
                        <i class="fas fa-location-arrow"></i>
                        Số nhà / Tên đường <span class="req">*</span>
                    </label>
                    <input type="text" name="address" id="address"
                        class="{{ $errors->has('address') ? 'error' : '' }}"
                        placeholder="Ví dụ: 123 Nguyễn Huệ, Phường Bến Nghé"
                        value="{{ old('address', $user->address) }}">
                    @error('address')
                        <div class="field-error"><i class="fas fa-times-circle"></i>{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-profile-submit" id="submitBtn">
                    <i class="fas fa-check-circle"></i>
                    Xác nhận & Tiếp tục
                </button>
            </form>

            <div class="profile-note">
                <i class="fas fa-lock"></i>
                Thông tin mã hóa bảo mật, chỉ dùng cho mục đích giao hàng
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const provinceSelect = document.getElementById('province_id');
    const districtSelect = document.getElementById('district_id');
    const wardSelect     = document.getElementById('ward_id');
    const submitBtn      = document.getElementById('submitBtn');
    const BASE_URL       = '{{ url("admin/ajax") }}';

    function setLoading(sel, text) {
        sel.innerHTML = `<option>${text}</option>`;
        sel.disabled  = true;
    }

    function loadOptions(url, sel, placeholder, selectedId) {
        setLoading(sel, 'Đang tải...');
        fetch(url)
            .then(r => r.json())
            .then(data => {
                sel.innerHTML = `<option value="">${placeholder}</option>`;
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.text  = (item.prefix ? item.prefix + ' ' : '') + item.name;
                    if (selectedId && item.id == selectedId) opt.selected = true;
                    sel.appendChild(opt);
                });
                sel.disabled = false;
            })
            .catch(() => setLoading(sel, placeholder));
    }

    provinceSelect.addEventListener('change', function () {
        setLoading(districtSelect, '-- Chọn Quận / Huyện --');
        setLoading(wardSelect, '-- Chọn Xã / Phường --');
        if (this.value) {
            loadOptions(`${BASE_URL}/districts/${this.value}`, districtSelect, '-- Chọn Quận / Huyện --', null);
        }
    });

    districtSelect.addEventListener('change', function () {
        setLoading(wardSelect, '-- Chọn Xã / Phường --');
        if (this.value) {
            loadOptions(`${BASE_URL}/wards/${this.value}`, wardSelect, '-- Chọn Xã / Phường --', null);
        }
    });

    // Loading state khi submit
    document.getElementById('profileForm').addEventListener('submit', function () {
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
    });
})();
</script>
@endpush
