@extends('Core::Admin.layouts.app')

@section('title', 'Thông tin cá nhân')
@section('page-title', 'Thông tin cá nhân')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">
    <style>
        /* ===== PROFILE INFO PAGE ===== */
        .pi-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media(max-width:768px) { .pi-grid { grid-template-columns: 1fr; } }

        /* Header card */
        .pi-header-card {
            background: linear-gradient(135deg, #1a3a5c 0%, #2d6aa0 100%);
            border-radius: 14px;
            padding: 28px 32px;
            display: flex;
            align-items: center;
            gap: 22px;
            margin-bottom: 22px;
            color: #fff;
            flex-wrap: wrap;
        }
        .pi-avatar {
            width: 80px; height: 80px; border-radius: 50%;
            object-fit: cover; border: 3px solid rgba(255,255,255,.35);
            flex-shrink: 0;
        }
        .pi-avatar-placeholder {
            width: 80px; height: 80px; border-radius: 50%;
            background: rgba(255,255,255,.2); display: flex;
            align-items: center; justify-content: center;
            font-size: 32px; color: #fff; font-weight: 700;
            border: 3px solid rgba(255,255,255,.35); flex-shrink: 0;
        }
        .pi-header-info { flex: 1; min-width: 180px; }
        .pi-header-info .name { font-size: 20px; font-weight: 700; }
        .pi-header-info .meta { font-size: 13px; color: rgba(255,255,255,.7); margin-top: 4px; }
        .pi-header-info .meta i { margin-right: 4px; }
        .pi-header-wallet {
            text-align: right; min-width: 140px;
        }
        .pi-header-wallet .amount { font-size: 24px; font-weight: 800; color: #6ee7b7; }
        .pi-header-wallet .lbl { font-size: 12px; color: rgba(255,255,255,.6); }
        @media(max-width:520px) {
            .pi-header-card { padding: 20px 16px; gap: 14px; }
            .pi-header-wallet { text-align: left; }
        }

        /* Referral card */
        .pi-ref-card {
            background: #fff; border-radius: 12px; padding: 18px 22px;
            margin-bottom: 22px; box-shadow: 0 2px 12px rgba(0,0,0,.06);
            border: 1px solid #e8edf2;
        }
        .pi-ref-label {
            font-size: 11px; font-weight: 700; color: #8a9bb0;
            text-transform: uppercase; letter-spacing: .5px; margin-bottom: 10px;
        }
        .pi-ref-label i { color: #4e8ef7; margin-right: 4px; }
        .pi-ref-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .pi-ref-link {
            flex: 1; background: #f0f4ff; border: 1.5px solid #d4e0f7;
            border-radius: 8px; padding: 10px 14px; font-size: 12px;
            color: #4e8ef7; word-break: break-all; font-family: monospace;
            min-width: 200px;
        }
        .pi-ref-copy {
            background: linear-gradient(135deg,#4e8ef7,#6c63ff); color: #fff;
            border: none; border-radius: 8px; padding: 10px 20px; font-size: 13px;
            font-weight: 600; cursor: pointer; white-space: nowrap; font-family: inherit;
            transition: .2s;
        }
        .pi-ref-copy:hover { opacity: .9; transform: translateY(-1px); }
        .pi-ref-hint { font-size: 11px; color: #8a9bb0; margin-top: 8px; }
        .pi-ref-hint i { margin-right: 3px; }

        /* Section card */
        .pi-section {
            background: #fff; border-radius: 12px; padding: 22px 26px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06); border: 1px solid #e8edf2;
        }
        .pi-section-title {
            font-size: 13px; font-weight: 700; color: #1e2a3a;
            margin-bottom: 18px; display: flex; align-items: center; gap: 8px;
            padding-bottom: 12px; border-bottom: 1px solid #f0f2f5;
        }
        .pi-section-title i { color: #4e8ef7; font-size: 15px; }

        /* Form fields */
        .pi-field { margin-bottom: 16px; }
        .pi-field label {
            display: block; font-size: 12px; font-weight: 600;
            color: #5a6a7e; margin-bottom: 6px;
            text-transform: uppercase; letter-spacing: .3px;
        }
        .pi-field label i { color: #4e8ef7; margin-right: 5px; font-size: 12px; }
        .pi-field input, .pi-field select {
            width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0;
            border-radius: 9px; font-size: 13.5px; font-family: 'Segoe UI', sans-serif;
            background: #f8fafc; color: #1e2a3a; transition: .2s; outline: none;
            appearance: none; -webkit-appearance: none;
        }
        .pi-field input:focus, .pi-field select:focus {
            border-color: #4e8ef7; background: #fff;
            box-shadow: 0 0 0 3px rgba(78,142,247,.1);
        }
        .pi-field input:read-only {
            background: #f1f3f7; color: #8a9bb0; cursor: not-allowed;
        }
        .pi-field .err { color: #ef4444; font-size: 12px; margin-top: 4px; }
        .pi-field .err i { margin-right: 3px; }
        .pi-field .help { font-size: 11px; color: #b0bec5; margin-top: 3px; }

        .pi-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media(max-width:520px) { .pi-row { grid-template-columns: 1fr; } }

        /* Select arrow */
        .pi-select-wrap { position: relative; }
        .pi-select-wrap::after {
            content: '\f107'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
            position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
            color: #8a9bb0; pointer-events: none; font-size: 13px;
        }
        .pi-select-wrap select { padding-right: 36px; }

        /* Submit */
        .pi-submit {
            width: 100%; padding: 12px; margin-top: 8px;
            background: linear-gradient(135deg, #4e8ef7, #6c63ff); color: #fff;
            border: none; border-radius: 10px; font-size: 14px; font-weight: 700;
            font-family: 'Segoe UI', sans-serif; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: .2s; box-shadow: 0 4px 16px rgba(78,142,247,.25);
        }
        .pi-submit:hover { opacity: .92; transform: translateY(-1px); }
        .pi-submit:active { transform: translateY(0); }
        .pi-submit.loading { pointer-events: none; opacity: .7; }

        /* Alert */
        .pi-alert-success {
            background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 9px;
            padding: 10px 16px; font-size: 13px; color: #15803d; margin-bottom: 18px;
            display: flex; align-items: center; gap: 8px;
        }
        .pi-alert-success i { color: #22c55e; }

        /* Upload */
        .pi-avatar-upload {
            display: flex; align-items: center; gap: 14px; margin-bottom: 16px;
        }
        .pi-avatar-upload label {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; background: #f0f4ff; border: 1.5px dashed #4e8ef7;
            border-radius: 8px; font-size: 12px; font-weight: 600; color: #4e8ef7;
            cursor: pointer; transition: .2s; text-transform: none; letter-spacing: 0;
        }
        .pi-avatar-upload label:hover { background: #e0ebff; }
        .pi-avatar-upload label i { font-size: 14px; }
        .pi-avatar-upload input[type="file"] { display: none; }
        .pi-avatar-preview {
            width: 56px; height: 56px; border-radius: 50%; object-fit: cover;
            border: 2px solid #e2e8f0;
        }
    </style>
@endpush

@section('content')

    {{-- THÔNG BÁO --}}
    @if(session('success'))
        <div class="pi-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    {{-- HEADER: Avatar + Tên + Ví --}}
    <div class="pi-header-card">
        @if($user->image)
            <img src="{{ asset('storage/'.$user->image) }}" class="pi-avatar">
        @else
            <div class="pi-avatar-placeholder">{{ strtoupper(substr($user->name,0,1)) }}</div>
        @endif
        <div class="pi-header-info">
            <div class="name">{{ $user->name }}</div>
            <div class="meta"><i class="fas fa-envelope"></i> {{ $user->email }}</div>
            @if($user->tel)<div class="meta"><i class="fas fa-phone"></i> {{ $user->tel }}</div>@endif
            @if($user->level)<div class="meta"><i class="fas fa-star"></i> {{ $user->level }}</div>@endif
        </div>
        <div class="pi-header-wallet">
            <div class="amount">{{ number_format($user->vi_tien ?? 0, 0, ',', '.') }}₫</div>
            <div class="lbl">Số dư ví</div>
        </div>
    </div>

    {{-- LINK GIỚI THIỆU --}}
    @php
        $refParam = $user->tel ?: $user->id;
        $refLink  = route('admin.register') . '?ref=' . urlencode($refParam);
    @endphp
    <div class="pi-ref-card">
        <div class="pi-ref-label"><i class="fas fa-share-alt"></i> Link giới thiệu của bạn</div>
        <div class="pi-ref-row">
            <div class="pi-ref-link" id="ref-link-box">{{ $refLink }}</div>
            <button onclick="copyRefLink()" id="btn-copy-ref" class="pi-ref-copy">
                <i class="fas fa-copy"></i> Copy link
            </button>
        </div>
        <div class="pi-ref-hint"><i class="fas fa-info-circle"></i> Gửi link này cho người muốn đăng ký — SĐT của bạn sẽ tự điền vào ô giới thiệu.</div>
    </div>

    {{-- FORM CHỈNH SỬA --}}
    <form action="{{ route('admin.profile.info.update') }}" method="POST" enctype="multipart/form-data" id="piForm">
        @csrf

        <div class="pi-grid">
            {{-- CỘT TRÁI: Cá nhân + Ảnh --}}
            <div class="pi-section">
                <div class="pi-section-title"><i class="fas fa-user"></i> Thông tin cá nhân</div>

                {{-- Ảnh đại diện --}}
                <div class="pi-avatar-upload">
                    @if($user->image)
                        <img src="{{ asset('storage/'.$user->image) }}" class="pi-avatar-preview" id="avatar-preview">
                    @else
                        <div class="pi-avatar-preview" id="avatar-preview" style="background:#e2e8f0;display:flex;align-items:center;justify-content:center;font-size:18px;color:#8a9bb0"><i class="fas fa-user"></i></div>
                    @endif
                    <label for="image-input"><i class="fas fa-camera"></i> Đổi ảnh đại diện</label>
                    <input type="file" name="image" id="image-input" accept="image/*">
                </div>

                <div class="pi-field">
                    <label><i class="fas fa-user"></i> Họ & Tên</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="err"><i class="fas fa-times-circle"></i> {{ $message }}</div>@enderror
                </div>

                <div class="pi-row">
                    <div class="pi-field">
                        <label><i class="fas fa-phone"></i> Số điện thoại</label>
                        <input type="tel" name="tel" value="{{ old('tel', $user->tel) }}">
                        @error('tel')<div class="err"><i class="fas fa-times-circle"></i> {{ $message }}</div>@enderror
                    </div>
                    <div class="pi-field">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" >
                        {{--                        <div class="help">Email không thể thay đổi</div>--}}
                    </div>
                </div>

                <div class="pi-row">
                    <div class="pi-field">
                        <label><i class="fas fa-calendar"></i> Ngày tham gia</label>
                        <input type="text" value="{{ $user->ngay_tham_gia ? \Carbon\Carbon::parse($user->ngay_tham_gia)->format('d/m/Y') : ($user->created_at ? $user->created_at->format('d/m/Y') : '—') }}" readonly>
                    </div>
                    <div class="pi-field">
                        <label><i class="fas fa-star"></i> Vai trò</label>
                        <input type="text" value="{{ $user->level ?: 'Người tiêu dùng' }}" readonly>
                    </div>
                </div>

                {{--                @if($user->parent)--}}
                {{--                    <div class="pi-field">--}}
                {{--                        <label><i class="fas fa-user-friends"></i> Người cha</label>--}}
                {{--                        <input type="text" value="{{ $user->parent->name }} ({{ $user->parent->tel ?: $user->parent->email }})" readonly>--}}
                {{--                    </div>--}}
                {{--                @endif--}}

                <div class="pi-field">
                    <label><i class="fas fa-user-friends"></i> Người chia sẻ</label>
                    <input type="text" value="{{ $user->inviteBy->name ?? '' }} ({{ $user->inviteBy->tel ?? '' }})  " readonly>
                </div>


                <div class="pi-row">
{{--                    <div class="pi-field">--}}
{{--                        <label><i class="fas fa-key"></i> Mật khẩu <small style="font-weight:400;color:#9ca3af">(Để trống nếu không đổi)</small></label>--}}
{{--                        <input type="password" value="" name="password" placeholder="***">--}}
{{--                    </div>--}}
{{--                    <div class="pi-field">--}}
{{--                        <label><i class="fas fa-lock"></i> Pin <small style="font-weight:400;color:#9ca3af">(Để trống nếu không đổi)</small></label>--}}
{{--                        <input type="password" value="" name="pin" placeholder="***">--}}
{{--                    </div>--}}

                    <div class="pi-field" style="grid-column: span 2;">
                        <label><i class="fas fa-id-card"></i> CCCD</label>
                        <input type="text" value="{{ old('cccd', $user->cccd) }}" name="cccd">
                    </div>
                </div>


            </div>

            {{-- CỘT PHẢI: Địa chỉ + Ngân hàng --}}
            <div>
                {{-- Địa chỉ --}}
                <div class="pi-section" style="margin-bottom:20px">
                    <div class="pi-section-title"><i class="fas fa-map-marker-alt"></i> Địa chỉ nhận hàng <p style="color:red">( ĐỊA CHỈ CŨ TRƯỚC SÁP NHẬP )</p> </div>

                    <div class="pi-field">
                        <label><i class="fas fa-city"></i> Tỉnh / Thành phố</label>
                        <div class="pi-select-wrap">
                            <select name="province_id" id="province_id">
                                <option value="">— Chọn tỉnh/thành —</option>
                                @foreach($provinces as $p)
                                    <option value="{{ $p->id }}" {{ old('province_id', $user->province_id) == $p->id ? 'selected' : '' }}>{{ $p->type }} {{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pi-row">
                        <div class="pi-field">
                            <label><i class="fas fa-map"></i> Quận / Huyện</label>
                            <div class="pi-select-wrap">
                                <select name="district_id" id="district_id" {{ $districts->isEmpty() ? 'disabled' : '' }}>
                                    <option value="">— Chọn quận/huyện —</option>
                                    @foreach($districts as $d)
                                        <option value="{{ $d->id }}" {{ old('district_id', $user->district_id) == $d->id ? 'selected' : '' }}>{{ $d->prefix }} {{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="pi-field">
                            <label><i class="fas fa-home"></i> Xã / Phường</label>
                            <div class="pi-select-wrap">
                                <select name="ward_id" id="ward_id" {{ $wards->isEmpty() ? 'disabled' : '' }}>
                                    <option value="">— Chọn phường/xã —</option>
                                    @foreach($wards as $w)
                                        <option value="{{ $w->id }}" {{ old('ward_id', $user->ward_id) == $w->id ? 'selected' : '' }}>{{ $w->prefix }} {{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pi-field">
                        <label><i class="fas fa-location-arrow"></i> Địa chỉ cụ thể <p style="color:red">( ghi rõ số nhà, địa chỉ nhân hàng )</p></label>
                        <input type="text" name="address" value="{{ old('address', $user->address) }}" placeholder="Số nhà, tên đường...">
                        @error('address')<div class="err"><i class="fas fa-times-circle"></i> {{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Ngân hàng --}}

                {{-- Ngân hàng --}}
                <div class="pi-section">
                    <div class="pi-section-title">
                        <i class="fas fa-university"></i>
                        Tài khoản ngân hàng
                    </div>

                    {{-- KHU VỰC NGÂN HÀNG --}}
                    <div class="pi-field">
                        <label>
                            <i class="fas fa-map-marker-alt"></i>
                            Khu vực ngân hàng
                            <small style="color:#9ca3af;font-weight:400">
                                (thành phố/tỉnh nơi có chi nhánh)
                            </small>
                        </label>

                        <div class="pi-select-wrap">
                            <select
                                    name="bank_area_id"
                                    id="bank_area_id"
                            >
                                <option value="">— Chọn khu vực ngân hàng —</option>

                                @foreach($bankCities as $city)
                                    <option
                                            value="{{ $city->id }}"
                                            {{ old('bank_area_id', $userBankCityId) == $city->id ? 'selected' : '' }}
                                    >
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @error('bank_area_id')
                        <div class="err">
                            <i class="fas fa-times-circle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>


                    {{-- NGÂN HÀNG --}}
                    <div class="pi-field">
                        <label>
                            <i class="fas fa-university"></i>
                            Ngân hàng
                        </label>

                        <div class="pi-select-wrap">
                            <select
                                    name="tong_ngan_hang_id"
                                    id="tong_ngan_hang_id"
                            >
                                <option value="">— Chọn ngân hàng —</option>

                                @foreach($ngan_hangs as $ngan_hangsItem)
                                    <option
                                            value="{{ $ngan_hangsItem->id }}"
                                            {{ old('tong_ngan_hang_id', $userTongNganHangId) == $ngan_hangsItem->id ? 'selected' : '' }}
                                    >
                                        {{ $ngan_hangsItem->name }} - {{ $ngan_hangsItem->ten_viet_tat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @error('tong_ngan_hang_id')
                        <div class="err">
                            <i class="fas fa-times-circle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>


                    {{-- CHI NHÁNH NGÂN HÀNG --}}
                    <div class="pi-field">
                        <label>
                            <i class="fas fa-code-branch"></i>
                            Chi nhánh ngân hàng
                        </label>

                        <div class="pi-select-wrap">
                            <select
                                    name="ngan_hang_chi_nhanh_id"
                                    id="ngan_hang_chi_nhanh_id"
                            >
                                <option value="">— Chọn chi nhánh ngân hàng —</option>
                            </select>
                        </div>

                        @error('ngan_hang_chi_nhanh_id')
                        <div class="err">
                            <i class="fas fa-times-circle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>


                    {{-- TÊN NGÂN HÀNG --}}
                    <input
                            type="hidden"
                            name="ngan_hang"
                            id="ngan_hang_name"
                            value="{{ old('ngan_hang', $user->ngan_hang) }}"
                    >

                    {{-- TÊN CHI NHÁNH --}}
                    <input
                            type="hidden"
                            name="chi_nhanh_ngan_hang"
                            id="chi_nhanh_ngan_hang"
                            value="{{ old('chi_nhanh_ngan_hang', $user->chi_nhanh_ngan_hang) }}"
                    >


                    {{-- SỐ TÀI KHOẢN --}}
                    <div class="pi-field">
                        <label>
                            <i class="fas fa-credit-card"></i>
                            Số tài khoản
                        </label>

                        <input
                                type="text"
                                name="stk"
                                id="stk"
                                value="{{ old('stk', $user->stk) }}"
                                placeholder="Số tài khoản ngân hàng"
                                inputmode="numeric"
                        >

                        @error('stk')
                        <div class="err">
                            <i class="fas fa-times-circle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>


                    {{-- CHỦ TÀI KHOẢN --}}
                    <div class="pi-field">
                        <label>
                            <i class="fas fa-user-check"></i>
                            Tên chủ tài khoản
                        </label>

                        <input
                                type="text"
                                name="chu_tk"
                                id="chu_tk"
                                value="{{ old('chu_tk', $user->chu_tk) }}"
                                placeholder="NGUYEN VAN A (viết hoa, không dấu)"
                        >
                    </div>

                </div>


            </div>
        </div>

        <div style="max-width:400px;margin:24px auto 0">
            <button type="submit" class="pi-submit" id="piSubmitBtn">
                <i class="fas fa-save"></i> Lưu thông tin
            </button>
        </div>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
@endsection

@push('scripts')
    <script>
        /* ── Copy referral link ── */
        function copyRefLink() {
            const link = document.getElementById('ref-link-box').textContent.trim();
            const btn  = document.getElementById('btn-copy-ref');
            navigator.clipboard.writeText(link).then(() => {
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Đã copy!';
                btn.style.background = 'linear-gradient(135deg,#22c55e,#16a34a)';
                setTimeout(() => { btn.innerHTML = orig; btn.style.background = ''; }, 2000);
            });
        }

        /* ── Avatar preview ── */
        document.getElementById('image-input').addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                const el = document.getElementById('avatar-preview');
                if (el.tagName === 'IMG') {
                    el.src = e.target.result;
                } else {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'pi-avatar-preview';
                    img.id = 'avatar-preview';
                    el.replaceWith(img);
                }
            };
            reader.readAsDataURL(file);
        });

        /* ── Address cascading selects ── */
        /* =========================================================
   * ADDRESS SELECTS
   * Tỉnh -> Huyện -> Xã
   * ========================================================= */

        const BASE = '{{ url("admin/ajax") }}';

        let provinceSelect;
        let districtSelect;
        let wardSelect;

        document.addEventListener('DOMContentLoaded', function () {

            /*
             * TỈNH / THÀNH PHỐ
             */
            provinceSelect = new TomSelect('#province_id', {
                placeholder: '— Tìm tỉnh/thành phố —',
                searchField: ['text'],
                maxOptions: 100,
                create: false,
                allowEmptyOption: true
            });


            /*
             * QUẬN / HUYỆN
             */
            districtSelect = new TomSelect('#district_id', {
                placeholder: '— Tìm quận/huyện —',
                searchField: ['text'],
                maxOptions: 500,
                create: false,
                allowEmptyOption: true
            });


            /*
             * XÃ / PHƯỜNG
             */
            wardSelect = new TomSelect('#ward_id', {
                placeholder: '— Tìm xã/phường —',
                searchField: ['text'],
                maxOptions: 1000,
                create: false,
                allowEmptyOption: true
            });


            /*
             * Khi chọn TỈNH
             */
            provinceSelect.on('change', function (provinceId) {

                clearSelect(
                    districtSelect,
                    '— Chọn quận/huyện —'
                );

                clearSelect(
                    wardSelect,
                    '— Chọn phường/xã —'
                );

                if (!provinceId) {
                    return;
                }

                loadDistricts(provinceId);
            });


            /*
             * Khi chọn HUYỆN
             */
            districtSelect.on('change', function (districtId) {

                clearSelect(
                    wardSelect,
                    '— Chọn phường/xã —'
                );

                if (!districtId) {
                    return;
                }

                loadWards(districtId);
            });

        });


        /*
         * Reset select
         */
        function clearSelect(select, placeholder) {

            select.clear();
            select.clearOptions();

            select.addOption({
                value: '',
                text: placeholder
            });

            select.setValue('');

            select.disable();
        }


        /*
         * Load quận huyện
         */
        function loadDistricts(provinceId) {

            districtSelect.clear();
            districtSelect.clearOptions();

            districtSelect.addOption({
                value: '',
                text: 'Đang tải quận/huyện...'
            });

            districtSelect.setValue('');
            districtSelect.disable();

            fetch(`${BASE}/districts/${provinceId}`)
                .then(response => {

                    if (!response.ok) {
                        throw new Error('Không tải được quận/huyện');
                    }

                    return response.json();
                })
                .then(data => {

                    districtSelect.clear();
                    districtSelect.clearOptions();

                    districtSelect.addOption({
                        value: '',
                        text: '— Chọn quận/huyện —'
                    });

                    data.forEach(item => {

                        districtSelect.addOption({
                            value: item.id,
                            text: `${item.prefix ?? ''} ${item.name}`.trim()
                        });

                    });

                    districtSelect.enable();
                })
                .catch(error => {

                    console.error(error);

                    districtSelect.clear();
                    districtSelect.clearOptions();

                    districtSelect.addOption({
                        value: '',
                        text: 'Không tải được dữ liệu'
                    });
                });
        }


        /*
         * Load xã phường
         */
        function loadWards(districtId) {

            wardSelect.clear();
            wardSelect.clearOptions();

            wardSelect.addOption({
                value: '',
                text: 'Đang tải phường/xã...'
            });

            wardSelect.setValue('');
            wardSelect.disable();

            fetch(`${BASE}/wards/${districtId}`)
                .then(response => {

                    if (!response.ok) {
                        throw new Error('Không tải được phường/xã');
                    }

                    return response.json();
                })
                .then(data => {

                    wardSelect.clear();
                    wardSelect.clearOptions();

                    wardSelect.addOption({
                        value: '',
                        text: '— Chọn phường/xã —'
                    });

                    data.forEach(item => {

                        wardSelect.addOption({
                            value: item.id,
                            text: `${item.prefix ?? ''} ${item.name}`.trim()
                        });

                    });

                    wardSelect.enable();
                })
                .catch(error => {

                    console.error(error);

                    wardSelect.clear();
                    wardSelect.clearOptions();

                    wardSelect.addOption({
                        value: '',
                        text: 'Không tải được dữ liệu'
                    });
                });
        }

        /* =========================================================
         * BANK
         *
         * Khu vực ngân hàng -> AJAX lấy chi nhánh
         *
         * Ngân hàng độc lập, không phụ thuộc khu vực
         * ========================================================= */

        let bankAreaSelect;
        let bankSelect;
        let branchSelect;

        document.addEventListener('DOMContentLoaded', function () {

            /* =====================================================
             * KHU VỰC NGÂN HÀNG
             * ===================================================== */

            bankAreaSelect = new TomSelect('#bank_area_id', {
                placeholder: '— Tìm khu vực ngân hàng —',
                searchField: ['text'],
                maxOptions: 100,
                create: false,
                allowEmptyOption: true
            });


            /* =====================================================
             * NGÂN HÀNG
             *
             * ĐỘC LẬP
             * Không phụ thuộc khu vực
             * ===================================================== */

            bankSelect = new TomSelect('#tong_ngan_hang_id', {
                placeholder: '— Tìm ngân hàng —',
                searchField: ['text'],
                maxOptions: 500,
                create: false,
                allowEmptyOption: true
            });


            /* =====================================================
             * CHI NHÁNH NGÂN HÀNG
             * ===================================================== */

            branchSelect = new TomSelect('#ngan_hang_chi_nhanh_id', {
                placeholder: '— Tìm chi nhánh ngân hàng —',
                searchField: ['text'],
                maxOptions: 2000,
                create: false,
                allowEmptyOption: true
            });


            /* =====================================================
             * CHỌN KHU VỰC NGÂN HÀNG
             *
             * => AJAX lấy danh sách chi nhánh
             * ===================================================== */

            bankAreaSelect.on('change', function (areaId) {

                clearBranches();

                if (!areaId) {
                    return;
                }

                loadBranches(areaId);
            });


            /* =====================================================
             * CHỌN NGÂN HÀNG
             *
             * => KHÔNG AJAX
             * => KHÔNG load chi nhánh
             * ===================================================== */

            bankSelect.on('change', function () {

                updateBankName();

            });


            /* =====================================================
             * LOAD CHI NHÁNH THEO KHU VỰC
             * ===================================================== */

            function loadBranches(areaId) {

                branchSelect.clear();
                branchSelect.clearOptions();

                branchSelect.addOption({
                    value: '',
                    text: 'Đang tải chi nhánh ngân hàng...'
                });

                branchSelect.setValue('');
                branchSelect.disable();

                const url = `/admin/ajax/branches/${areaId}`;

                fetch(url)
                    .then(response => {

                        if (!response.ok) {
                            throw new Error('Không tải được chi nhánh ngân hàng');
                        }

                        return response.json();
                    })
                    .then(data => {

                        branchSelect.clear();
                        branchSelect.clearOptions();

                        branchSelect.addOption({
                            value: '',
                            text: '— Chọn chi nhánh ngân hàng —'
                        });

                        data.forEach(branch => {

                            branchSelect.addOption({
                                value: branch.id,
                                text: branch.name
                            });

                        });

                        branchSelect.enable();
                    })
                    .catch(error => {

                        console.error(error);

                        branchSelect.clear();
                        branchSelect.clearOptions();

                        branchSelect.addOption({
                            value: '',
                            text: 'Không tải được chi nhánh ngân hàng'
                        });
                    });
            }


            /* =====================================================
             * CHỌN CHI NHÁNH
             * ===================================================== */

            branchSelect.on('change', function (branchId) {

                updateBranchName();

            });


            /* =====================================================
             * CẬP NHẬT TÊN NGÂN HÀNG
             * ===================================================== */

            function updateBankName() {

                const bankId = bankSelect.getValue();

                if (!bankId) {

                    document.getElementById('ngan_hang_name').value = '';

                    return;
                }

                const bank = bankSelect.options[bankId];

                if (bank) {

                    document.getElementById('ngan_hang_name').value =
                        bank.text || '';

                }
            }


            /* =====================================================
             * CẬP NHẬT TÊN CHI NHÁNH
             * ===================================================== */

            function updateBranchName() {

                const branchId = branchSelect.getValue();

                if (!branchId) {

                    document.getElementById('chi_nhanh_ngan_hang').value = '';

                    return;
                }

                const branch = branchSelect.options[branchId];

                if (branch) {

                    document.getElementById('chi_nhanh_ngan_hang').value =
                        branch.text || '';

                }
            }


            /* =====================================================
             * LOAD DỮ LIỆU CŨ
             * ===================================================== */

            const oldAreaId =
                '{{ old('bank_area_id', $userBankCityId) }}';

            const oldBranchId =
                '{{ old('ngan_hang_chi_nhanh_id', $user->chi_nhanh_ngan_hang) }}';


            if (oldAreaId) {

                loadBranches(oldAreaId);

                setTimeout(function () {

                    if (oldBranchId) {

                        branchSelect.setValue(oldBranchId);

                        updateBranchName();

                    }

                    updateBankName();

                }, 500);
            }

        });


        /* =========================================================
         * RESET CHI NHÁNH
         * ========================================================= */

        function clearBranches() {

            if (!branchSelect) {
                return;
            }

            branchSelect.clear();
            branchSelect.clearOptions();

            branchSelect.addOption({
                value: '',
                text: '— Chọn chi nhánh ngân hàng —'
            });

            branchSelect.setValue('');
            branchSelect.disable();
        }



        function loadTongNganHangs(cityId) {

            const branchSel = document.getElementById('ngan_hang_id');

            branchSel.innerHTML =
                '<option value="">— Đang tải chi nhánh... —</option>';

            branchSel.disabled = true;

            if (!cityId) {
                branchSel.innerHTML =
                    '<option value="">— Chọn chi nhánh —</option>';

                branchSel.disabled = false;
                return;
            }

            const url = `/admin/ajax/branches/${cityId}`;

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Không tải được chi nhánh');
                    }

                    return response.json();
                })
                .then(list => {

                    branchSel.innerHTML =
                        '<option value="">— Chọn chi nhánh —</option>';

                    list.forEach(branch => {

                        const option = document.createElement('option');

                        option.value = branch.id;
                        option.textContent = branch.name;

                        branchSel.appendChild(option);
                    });

                    branchSel.disabled = false;
                })
                .catch(error => {

                    console.error(error);

                    branchSel.innerHTML =
                        '<option value="">— Không tải được chi nhánh —</option>';

                    branchSel.disabled = true;
                });
        }


    </script>
@endpush
