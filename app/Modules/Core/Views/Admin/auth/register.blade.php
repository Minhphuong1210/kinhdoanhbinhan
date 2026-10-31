<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;min-height:100vh;background:linear-gradient(115deg,#288098 50%,#125e7e 50%);display:flex;align-items:flex-start;justify-content:center;padding:32px 16px}
        .card{background:#fff;border-radius:16px;padding:36px 40px;width:100%;max-width:680px;box-shadow:0 4px 24px rgba(0,0,0,.08);border:1px solid #e8edf2}
        .logo{text-align:center;margin-bottom:28px}
        .logo img{height:52px;object-fit:contain;display:block;margin:0 auto 10px}
        .logo-icon{width:52px;height:52px;border-radius:13px;background:linear-gradient(135deg,#4e8ef7,#6c63ff);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;font-size:22px;color:#fff}
        .logo h2{font-size:20px;font-weight:700;color:#1e2a3a}
        .logo p{font-size:13px;color:#8a9bb0;margin-top:3px}

        .section-title{font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.8px;margin:22px 0 14px;display:flex;align-items:center;gap:8px}
        .section-title::after{content:'';flex:1;height:1px;background:#e8edf2}

        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        .form-row.full{grid-template-columns:1fr}
        .form-group{display:flex;flex-direction:column;gap:5px}
        .form-group label{font-size:12px;font-weight:600;color:#374151}
        .input-wrap{position:relative}
        .input-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:13px;pointer-events:none}
        .form-control{width:100%;padding:9px 14px 9px 36px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;font-family:inherit;color:#1e2a3a;background:#fff;transition:.2s;outline:none}
        .form-control:focus{border-color:#4e8ef7;box-shadow:0 0 0 3px rgba(78,142,247,.1)}
        .form-control.is-invalid{border-color:#ef4444}
        .err-msg{font-size:11px;color:#ef4444;margin-top:2px}

        .btn-submit{width:100%;padding:12px;background:linear-gradient(135deg,#4e8ef7,#6c63ff);color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;transition:.2s;margin-top:22px}
        .btn-submit:hover{opacity:.9;transform:translateY(-1px);box-shadow:0 4px 14px rgba(78,142,247,.3)}
        .alert-err{background:#fef2f2;border:1px solid #fecaca;border-radius:9px;padding:10px 14px;font-size:13px;color:#dc2626;margin-bottom:18px}
        .alert-err ul{list-style:none;padding:0}
        .footer-link{text-align:center;margin-top:18px;font-size:13px;color:#8a9bb0}
        .footer-link a{color:#4e8ef7;font-weight:600;text-decoration:none}
        @media(max-width:520px){.form-row{grid-template-columns:1fr}.card{padding:24px 16px}}
    </style>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">
</head>
<body>
<div class="card">
    <div class="logo">
        @if($logo)
            <img src="{{ cdn($logo) }}" alt="Logo">
        @else
            <div class="logo-icon"><i class="fas fa-user-plus"></i></div>
        @endif
        <h2>Đăng ký tài khoản</h2>
        <p>Điền thông tin để tạo tài khoản mới</p>
    </div>

    @if($errors->any())
        <div class="alert-err"><ul>@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('admin.register.submit') }}">
        @csrf

        {{-- CÁ NHÂN --}}
        <div class="section-title"><i class="fas fa-user" style="color:#4e8ef7"></i> Thông tin cá nhân</div>
        <div class="form-row">
            <div class="form-group">
                <label>Họ & Tên <span style="color:red">*</span></label>
                <div class="input-wrap"><i class="fas fa-user"></i>
                    <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}" placeholder="Nguyễn Văn A" required>
                </div>
                @error('name')<div class="err-msg">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Số điện thoại <span style="color:red">*</span></label>
                <div class="input-wrap"><i class="fas fa-phone"></i>
                    <input type="tel" name="tel" class="form-control {{ $errors->has('tel') ? 'is-invalid' : '' }}" value="{{ old('tel') }}" placeholder="0987654321" required>
                </div>
                @error('tel')<div class="err-msg">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="form-row" style="margin-top:12px">
            <div class="form-group">
                <label>Email <span style="color:red">*</span></label>
                <div class="input-wrap"><i class="fas fa-envelope"></i>
                    <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email') }}" placeholder="email@example.com" required>
                </div>
                @error('email')<div class="err-msg">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Số CCCD / CMND</label>
                <div class="input-wrap"><i class="fas fa-id-card"></i>
                    <input type="text" name="cccd" class="form-control {{ $errors->has('cccd') ? 'is-invalid' : '' }}" value="{{ old('cccd') }}" placeholder="012345678910" maxlength="20" inputmode="numeric">
                </div>
                @error('cccd')<div class="err-msg">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- BẢO MẬT --}}
        <div class="section-title"><i class="fas fa-lock" style="color:#4e8ef7"></i> Mật khẩu & Bảo mật</div>
        <div class="form-row">
            <div class="form-group">
                <label>Mật khẩu <span style="color:red">*</span></label>
                <div class="input-wrap"><i class="fas fa-key"></i>
                    <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Tối thiểu 6 ký tự" required>
                </div>
                @error('password')<div class="err-msg">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Nhập lại mật khẩu <span style="color:red">*</span></label>
                <div class="input-wrap"><i class="fas fa-key"></i>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu" required>
                </div>
            </div>
        </div>
        <div class="form-row" style="margin-top:12px">
            <div class="form-group">
                <label>Mã PIN <small style="color:#9ca3af;font-weight:400">(4–6 chữ số)</small></label>
                <div class="input-wrap"><i class="fas fa-shield-alt"></i>
                    <input type="password" name="pin" class="form-control" maxlength="6" placeholder="••••••" inputmode="numeric">
                </div>
                @error('pin')<div class="err-msg">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>SĐT người giới thiệu</label>
                <div class="input-wrap"><i class="fas fa-link"></i>
                    <input type="text" name="invite_by" class="form-control" value="{{ old('invite_by', $refCode) }}" placeholder="Số điện thoại">
                </div>
            </div>
        </div>

        {{-- ĐỊA CHỈ --}}
        {{-- ĐỊA CHỈ --}}
        <div class="section-title">
            <i class="fas fa-map-marker-alt" style="color:#4e8ef7"></i>
            Địa chỉ nhận hàng
        </div>

        <div class="form-row">

            {{-- TỈNH --}}
            <div class="form-group">
                <label>Tỉnh / Thành phố</label>

                <div class="input-wrap">
                    <i class="fas fa-city"></i>

                    <select
                            name="province_id"
                            id="province_id"
                            class="form-control"
                    >
                        <option value="">— Chọn tỉnh/thành —</option>

                        @foreach($provinces as $p)
                            <option
                                    value="{{ $p->id }}"
                                    {{ old('province_id') == $p->id ? 'selected' : '' }}
                            >
                                {{ $p->type }} {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @error('province_id')
                <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>


            {{-- QUẬN --}}
            <div class="form-group">
                <label>Quận / Huyện</label>

                <div class="input-wrap">
                    <i class="fas fa-map"></i>

                    <select
                            name="district_id"
                            id="district_id"
                            class="form-control"
                    >
                        <option value="">— Chọn quận/huyện —</option>
                    </select>
                </div>

                @error('district_id')
                <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

        </div>


        <div class="form-row" style="margin-top:12px">

            {{-- XÃ --}}
            <div class="form-group">
                <label>Phường / Xã</label>

                <div class="input-wrap">
                    <i class="fas fa-street-view"></i>

                    <select
                            name="ward_id"
                            id="ward_id"
                            class="form-control"
                    >
                        <option value="">— Chọn phường/xã —</option>
                    </select>
                </div>

                @error('ward_id')
                <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>


            {{-- ĐỊA CHỈ --}}
            <div class="form-group">
                <label>Địa chỉ cụ thể</label>

                <div class="input-wrap">
                    <i class="fas fa-home"></i>

                    <input
                            type="text"
                            name="address"
                            class="form-control"
                            value="{{ old('address') }}"
                            placeholder="Số nhà, tên đường..."
                    >
                </div>

                @error('address')
                <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

        </div>

        {{-- NGÂN HÀNG --}}
        <div class="section-title"><i class="fas fa-university" style="color:#4e8ef7"></i> Tài khoản ngân hàng</div>

        {{-- KHU VỰC + NGÂN HÀNG --}}
        <div class="form-row">

            {{-- KHU VỰC NGÂN HÀNG --}}
            <div class="form-group">
                <label>
                    Khu vực ngân hàng
                    <small style="color:#9ca3af;font-weight:400">
                        (nơi có chi nhánh)
                    </small>
                </label>

                <div class="input-wrap">
                    <i class="fas fa-city"></i>

                    <select
                            name="bank_area_id"
                            id="bank_area_id"
                            class="form-control"
                    >
                        <option value="">— Chọn khu vực ngân hàng —</option>

                        @foreach($bankCities as $city)
                            <option
                                    value="{{ $city->id }}"
                                    {{ old('bank_area_id') == $city->id ? 'selected' : '' }}
                            >
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @error('bank_area_id')
                <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>


            {{-- NGÂN HÀNG --}}
            <div class="form-group">
                <label>Ngân hàng</label>

                <div class="input-wrap">
                    <i class="fas fa-landmark"></i>

                    <select
                            name="tong_ngan_hang_id"
                            id="tong_ngan_hang_id"
                            class="form-control"
                    >
                        <option value="">— Chọn ngân hàng —</option>

                        @foreach($ngan_hangs as $bank)
                            <option
                                    value="{{ $bank->id }}"
                                    {{ old('tong_ngan_hang_id') == $bank->id ? 'selected' : '' }}
                            >
                                {{ $bank->name }} - {{ $bank->ten_viet_tat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @error('tong_ngan_hang_id')
                <div class="err-msg">{{ $message }}</div>
                @enderror
            </div>

        </div>


        {{-- CHI NHÁNH + STK --}}
        <div class="form-row" style="margin-top:12px">

            {{-- CHI NHÁNH --}}
            <div class="form-group">

                <label>Chi nhánh ngân hàng</label>

                <div class="input-wrap">
                    <i class="fas fa-code-branch"></i>

                    <select
                            name="ngan_hang_chi_nhanh_id"
                            id="ngan_hang_chi_nhanh_id"
                            class="form-control"
                    >
                        <option value="">
                            — Chọn chi nhánh ngân hàng —
                        </option>
                    </select>
                </div>

                @error('ngan_hang_chi_nhanh_id')
                <div class="err-msg">{{ $message }}</div>
                @enderror

            </div>


            {{-- STK --}}
            <div class="form-group">

                <label>Số tài khoản</label>

                <div class="input-wrap">
                    <i class="fas fa-credit-card"></i>

                    <input
                            type="text"
                            name="stk"
                            class="form-control"
                            value="{{ old('stk') }}"
                            placeholder="Số tài khoản"
                            inputmode="numeric"
                    >
                </div>

                @error('stk')
                <div class="err-msg">{{ $message }}</div>
                @enderror

            </div>

        </div>


        {{-- HIDDEN --}}
        <input
                type="hidden"
                name="ngan_hang"
                id="ngan_hang_name"
                value="{{ old('ngan_hang') }}"
        >

        <input
                type="hidden"
                name="chi_nhanh_ngan_hang"
                id="chi_nhanh_ngan_hang"
                value="{{ old('chi_nhanh_ngan_hang') }}"
        >


       

        {{-- Tên chủ TK + hidden fields --}}
        <input type="hidden" name="ngan_hang" id="ngan_hang_name" value="{{ old('ngan_hang') }}">
        <input type="hidden" name="chi_nhanh_ngan_hang" id="chi_nhanh_ngan_hang" value="{{ old('chi_nhanh_ngan_hang') }}">
        <div class="form-row full" style="margin-top:12px">
            <div class="form-group">
                <label>Tên chủ tài khoản</label>
                <div class="input-wrap"><i class="fas fa-user-check"></i>
                    <input type="text" name="chu_tk" class="form-control" value="{{ old('chu_tk') }}" placeholder="NGUYEN VAN A (viết hoa, không dấu)">
                </div>
            </div>
        </div>

        <button type="submit" class="btn-submit"><i class="fas fa-user-plus"></i> Tạo tài khoản</button>
    </form>

    <div class="footer-link">Đã có tài khoản? <a href="{{ route('admin.login') }}">Đăng nhập ngay</a></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        /* =========================================================
         * BASE AJAX
         * ========================================================= */

        const BASE = '{{ url("admin/ajax") }}';


        /* =========================================================
         * ĐỊA CHỈ
         *
         * Tỉnh -> Huyện -> Xã
         * ========================================================= */

        let provinceSelect;
        let districtSelect;
        let wardSelect;


        /* =========================================================
         * TOM SELECT - ĐỊA CHỈ
         * ========================================================= */

        provinceSelect = new TomSelect('#province_id', {

            placeholder: '— Tìm tỉnh/thành phố —',

            searchField: ['text'],

            maxOptions: 100,

            create: false,

            allowEmptyOption: true
        });


        districtSelect = new TomSelect('#district_id', {

            placeholder: '— Tìm quận/huyện —',

            searchField: ['text'],

            maxOptions: 500,

            create: false,

            allowEmptyOption: true
        });


        wardSelect = new TomSelect('#ward_id', {

            placeholder: '— Tìm phường/xã —',

            searchField: ['text'],

            maxOptions: 1000,

            create: false,

            allowEmptyOption: true
        });


        /* =========================================================
         * TỈNH THAY ĐỔI
         * ========================================================= */

        provinceSelect.on('change', function (provinceId) {

            clearDistricts();

            clearWards();

            if (!provinceId) {
                return;
            }

            loadDistricts(provinceId);
        });


        /* =========================================================
         * HUYỆN THAY ĐỔI
         * ========================================================= */

        districtSelect.on('change', function (districtId) {

            clearWards();

            if (!districtId) {
                return;
            }

            loadWards(districtId);
        });


        /* =========================================================
         * RESET HUYỆN
         * ========================================================= */

        function clearDistricts() {

            districtSelect.clear();

            districtSelect.clearOptions();

            districtSelect.addOption({
                value: '',
                text: '— Chọn quận/huyện —'
            });

            districtSelect.setValue('');

            districtSelect.disable();
        }


        /* =========================================================
         * RESET XÃ
         * ========================================================= */

        function clearWards() {

            wardSelect.clear();

            wardSelect.clearOptions();

            wardSelect.addOption({
                value: '',
                text: '— Chọn phường/xã —'
            });

            wardSelect.setValue('');

            wardSelect.disable();
        }


        /* =========================================================
         * LOAD QUẬN / HUYỆN
         * ========================================================= */

        function loadDistricts(provinceId, selectedDistrictId = null) {

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

                            text:
                                `${item.prefix ?? ''} ${item.name}`.trim()
                        });

                    });


                    districtSelect.enable();


                    /* Khôi phục old */
                    if (selectedDistrictId) {

                        districtSelect.setValue(
                            String(selectedDistrictId)
                        );

                        loadWards(
                            selectedDistrictId,
                                @json(old('ward_id'))
                        );
                    }

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


        /* =========================================================
         * LOAD PHƯỜNG / XÃ
         * ========================================================= */

        function loadWards(districtId, selectedWardId = null) {

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

                            text:
                                `${item.prefix ?? ''} ${item.name}`.trim()
                        });

                    });


                    wardSelect.enable();


                    /* Khôi phục old */
                    if (selectedWardId) {

                        wardSelect.setValue(
                            String(selectedWardId)
                        );
                    }

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
         * KHÔI PHỤC ĐỊA CHỈ KHI VALIDATION FAIL
         * ========================================================= */

        const oldProvinceId =
                @json(old('province_id'));

        const oldDistrictId =
                @json(old('district_id'));

        const oldWardId =
                @json(old('ward_id'));


        if (oldProvinceId) {

            provinceSelect.setValue(
                String(oldProvinceId)
            );


            if (oldDistrictId) {

                loadDistricts(
                    oldProvinceId,
                    oldDistrictId
                );

            } else {

                loadDistricts(oldProvinceId);
            }
        }


        /* =========================================================
         * NGÂN HÀNG
         *
         * Khu vực -> Chi nhánh
         *
         * Ngân hàng độc lập
         * ========================================================= */

        let bankAreaSelect;

        let bankSelect;

        let branchSelect;


        /* =========================================================
         * KHU VỰC NGÂN HÀNG
         * ========================================================= */

        bankAreaSelect = new TomSelect('#bank_area_id', {

            placeholder: '— Tìm khu vực ngân hàng —',

            searchField: ['text'],

            maxOptions: 100,

            create: false,

            allowEmptyOption: true
        });


        /* =========================================================
         * NGÂN HÀNG
         * ========================================================= */

        bankSelect = new TomSelect('#tong_ngan_hang_id', {

            placeholder: '— Tìm ngân hàng —',

            searchField: ['text'],

            maxOptions: 500,

            create: false,

            allowEmptyOption: true
        });


        /* =========================================================
         * CHI NHÁNH
         * ========================================================= */

        branchSelect = new TomSelect(
            '#ngan_hang_chi_nhanh_id',
            {

                placeholder:
                    '— Tìm chi nhánh ngân hàng —',

                searchField: ['text'],

                maxOptions: 2000,

                create: false,

                allowEmptyOption: true
            }
        );


        /* =========================================================
         * CHỌN KHU VỰC
         * ========================================================= */

        bankAreaSelect.on('change', function (areaId) {

            clearBranches();

            if (!areaId) {
                return;
            }

            loadBranches(areaId);
        });


        /* =========================================================
         * CHỌN NGÂN HÀNG
         *
         * KHÔNG AJAX
         * ========================================================= */

        bankSelect.on('change', function () {

            updateBankName();
        });


        /* =========================================================
         * CHỌN CHI NHÁNH
         * ========================================================= */

        branchSelect.on('change', function () {

            updateBranchName();
        });


        /* =========================================================
         * RESET CHI NHÁNH
         * ========================================================= */

        function clearBranches() {

            branchSelect.clear();

            branchSelect.clearOptions();

            branchSelect.addOption({

                value: '',

                text: '— Chọn chi nhánh ngân hàng —'
            });

            branchSelect.setValue('');

            branchSelect.disable();


            document.getElementById(
                'chi_nhanh_ngan_hang'
            ).value = '';
        }


        /* =========================================================
         * LOAD CHI NHÁNH
         * ========================================================= */

        function loadBranches(
            areaId,
            selectedBranchId = null
        ) {

            branchSelect.clear();

            branchSelect.clearOptions();

            branchSelect.addOption({

                value: '',

                text: 'Đang tải chi nhánh ngân hàng...'
            });

            branchSelect.setValue('');

            branchSelect.disable();


            fetch(`${BASE}/branches/${areaId}`)

                .then(response => {

                    if (!response.ok) {

                        throw new Error(
                            'Không tải được chi nhánh ngân hàng'
                        );
                    }

                    return response.json();
                })

                .then(data => {

                    branchSelect.clear();

                    branchSelect.clearOptions();

                    branchSelect.addOption({

                        value: '',

                        text:
                            '— Chọn chi nhánh ngân hàng —'
                    });


                    data.forEach(branch => {

                        branchSelect.addOption({

                            value: branch.id,

                            text: branch.name
                        });
                    });


                    branchSelect.enable();


                    /* Khôi phục old */
                    if (selectedBranchId) {

                        const exists = data.some(
                            branch =>
                                String(branch.id) ===
                                String(selectedBranchId)
                        );


                        if (exists) {

                            branchSelect.setValue(
                                String(selectedBranchId)
                            );

                            updateBranchName();
                        }
                    }

                })

                .catch(error => {

                    console.error(error);

                    branchSelect.clear();

                    branchSelect.clearOptions();

                    branchSelect.addOption({

                        value: '',

                        text: 'Không tải được chi nhánh'
                    });
                });
        }


        /* =========================================================
         * CẬP NHẬT TÊN NGÂN HÀNG
         * ========================================================= */

        function updateBankName() {

            const bankId =
                bankSelect.getValue();


            if (!bankId) {

                document.getElementById(
                    'ngan_hang_name'
                ).value = '';

                return;
            }


            const bank =
                bankSelect.options[bankId];


            if (bank) {

                document.getElementById(
                    'ngan_hang_name'
                ).value = bank.text || '';
            }
        }


        /* =========================================================
         * CẬP NHẬT TÊN CHI NHÁNH
         * ========================================================= */

        function updateBranchName() {

            const branchId =
                branchSelect.getValue();


            if (!branchId) {

                document.getElementById(
                    'chi_nhanh_ngan_hang'
                ).value = '';

                return;
            }


            const branch =
                branchSelect.options[branchId];


            if (branch) {

                document.getElementById(
                    'chi_nhanh_ngan_hang'
                ).value =
                    branch.text || '';
            }
        }


        /* =========================================================
         * KHÔI PHỤC NGÂN HÀNG KHI VALIDATION FAIL
         * ========================================================= */

        const oldBankAreaId =
                @json(old('bank_area_id'));

        const oldBankId =
                @json(old('tong_ngan_hang_id'));

        const oldBranchId =
                @json(old('ngan_hang_chi_nhanh_id'));


        /* Chọn lại ngân hàng */
        if (oldBankId) {

            bankSelect.setValue(
                String(oldBankId)
            );

            updateBankName();
        }


        /* Chọn lại khu vực + chi nhánh */
        if (oldBankAreaId) {

            bankAreaSelect.setValue(
                String(oldBankAreaId)
            );


            loadBranches(
                oldBankAreaId,
                oldBranchId
            );
        }

    });
</script>


</body>
</html>
