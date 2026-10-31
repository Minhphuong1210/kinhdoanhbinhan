@extends('Core::Admin.layouts.app')
@section('title', 'Chuyển điểm nội bộ')
@section('page-title', 'Chuyển điểm nội bộ')
@section('content')



    <div style="max-width:520px;margin:0 auto">


        {{-- Số dư --}}
        <div
                style="background:#fff;border-radius:12px;padding:16px 20px;box-shadow:0 1px 8px rgba(0,0,0,.07);display:flex;align-items:center;gap:14px;margin-bottom:20px">
            <div
                    style="width:44px;height:44px;border-radius:10px;background:#dbeafe;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">
                <i class="fas fa-wallet"></i></div>
            <div>
                <div style="font-size:11px;color:#8a9bb0;margin-bottom:2px">Số dư ví hiện tại</div>
                <div style="font-size:22px;font-weight:800;color:#1e2a3a">
                    {{ number_format($user->vi_tien ?? 0, 0, ',', '.') }}₫</div>
            </div>
        </div>

        {{-- Form --}}
        <div class="card">
            <div class="card-header">
                <h5 style="margin:0"><i class="fas fa-exchange-alt"></i> Chuyển điểm nội bộ</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.chuyen-diem.store') }}" id="transferForm">
                    @csrf

                    {{-- Người nhận --}}
                    <div class="form-group">
                        <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block">
                            Số điện thoại / ID người nhận <span style="color:red">*</span>
                        </label>
                        <input type="text" name="nguoi_nhan" id="nguoi_nhan"
                               class="form-control {{ $errors->has('nguoi_nhan') ? 'is-invalid' : '' }}"
                               placeholder="Nhập SĐT hoặc ID..." value="{{ old('nguoi_nhan') }}" autocomplete="off">

                        {{-- Preview người nhận --}}
                        <div id="recipient-preview"
                             style="display:none;margin-top:10px;padding:10px 14px;background:#f0fdf4;border:1.5px solid #86efac;border-radius:8px;display:flex;align-items:center;gap:10px">
                            <div id="rcp-avatar"
                                 style="width:36px;height:36px;border-radius:50%;background:#16a34a;color:#fff;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;flex-shrink:0">
                            </div>
                            <div>
                                <div id="rcp-name" style="font-size:14px;font-weight:700;color:#15803d"></div>
                                <div id="rcp-tel" style="font-size:12px;color:#4ade80"></div>
                            </div>
                            <i class="fas fa-check-circle" style="margin-left:auto;color:#16a34a;font-size:18px"></i>
                        </div>

                        <div id="recipient-not-found"
                             style="display:none;margin-top:10px;padding:10px 14px;background:#fef2f2;border:1.5px solid #fca5a5;border-radius:8px;font-size:13px;color:#dc2626">
                            <i class="fas fa-times-circle"></i> Không tìm thấy người dùng này
                        </div>

                        @error('nguoi_nhan')
                        <div style="color:#dc2626;font-size:12px;margin-top:4px"><i class="fas fa-exclamation-circle"></i>
                            {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Số tiền --}}
                    <div class="form-group" style="margin-top:16px">
                        <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block">
                            Số tiền <span style="color:red">*</span>
                        </label>
                        <div style="position:relative">
                            <input type="number" name="so_tien" id="so_tien"
                                   class="form-control {{ $errors->has('so_tien') ? 'is-invalid' : '' }}"
                                   placeholder="Tối thiểu 1.000"  value="{{ old('so_tien') }}"
                                   style="padding-right:40px">
                            <span
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#8a9bb0;font-size:13px">₫</span>
                        </div>
                        {{-- Hiển thị số tiền đã format --}}
                        <div id="amount-display" style="font-size:12px;color:#8a9bb0;margin-top:4px"></div>
                        @error('so_tien')
                        <div style="color:#dc2626;font-size:12px;margin-top:4px"><i class="fas fa-exclamation-circle"></i>
                            {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Ví nhận tiền --}}
                    <div class="form-group" style="margin-top:16px">
                        <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block">
                            Ví nhận <span style="color:red">*</span>
                        </label>
                        <div style="display:flex;gap:15px">
                            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer">
                                <input type="radio" name="loai_vi" value="vi_tien" {{ old('loai_vi', 'vi_tien') == 'vi_tien' ? 'checked' : '' }}>
                                Ví tiền (Ví chính)
                            </label>
                            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer">
                                <input type="radio" name="loai_vi" value="vi_tien_mua_hang" {{ old('loai_vi') == 'vi_tien_mua_hang' ? 'checked' : '' }}>
                                Ví mua hàng
                            </label>
                        </div>
                        @error('loai_vi')
                        <div style="color:#dc2626;font-size:12px;margin-top:4px"><i class="fas fa-exclamation-circle"></i>
                            {{ $message }}</div>
                        @enderror
                    </div>

                    <div
                            style="margin-top:16px;padding:11px 14px;background:#f8faff;border-radius:8px;font-size:12px;color:#5a6a7e">
                        <i class="fas fa-info-circle" style="color:#4e8ef7"></i>
                        Điểm chuyển ngay, không thể hoàn tác. Kiểm tra kỹ thông tin người nhận.
                    </div>

                    <button type="submit" id="submitBtn" class="btn btn-primary"
                            style="margin-top:18px;width:100%;font-weight:700" disabled>
                        <i class="fas fa-paper-plane"></i> Xác nhận chuyển điểm
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const input = document.getElementById('nguoi_nhan');
            const preview = document.getElementById('recipient-preview');
            const notFound = document.getElementById('recipient-not-found');
            const rcpName = document.getElementById('rcp-name');
            const rcpTel = document.getElementById('rcp-tel');
            const rcpAvatar = document.getElementById('rcp-avatar');
            const submitBtn = document.getElementById('submitBtn');
            const amountDisp = document.getElementById('amount-display');
            const amountInp = document.getElementById('so_tien');
            const lookupUrl = "{{ route('admin.ajax.lookup-user') }}";
            const csrfToken = "{{ csrf_token() }}";

            let debounceTimer = null;
            let recipientFound = false;

            function hideAll() {
                preview.style.display = 'none';
                notFound.style.display = 'none';
            }

            function updateSubmit() {
                submitBtn.disabled = !recipientFound;
            }

            input.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                const q = this.value.trim();
                hideAll();
                recipientFound = false;
                updateSubmit();

                if (q.length < 3) return;

                debounceTimer = setTimeout(function () {
                    fetch(lookupUrl + '?q=' + encodeURIComponent(q), {
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.found) {
                                rcpName.textContent = data.name;
                                rcpTel.textContent = data.tel;
                                rcpAvatar.textContent = data.name.charAt(0).toUpperCase();
                                preview.style.display = 'flex';
                                notFound.style.display = 'none';
                                recipientFound = true;
                            } else {
                                preview.style.display = 'none';
                                notFound.style.display = 'block';
                                recipientFound = false;
                            }
                            updateSubmit();
                        })
                        .catch(() => { hideAll(); });
                }, 400);
            });

            // Format tiền
            amountInp.addEventListener('input', function () {
                const v = parseInt(this.value);
                amountDisp.textContent = v > 0 ? '= ' + v.toLocaleString('vi-VN') + '₫' : '';
            });

            // Allow submit if old value already set (validation error repopulate)
            @if(old('nguoi_nhan'))
                recipientFound = true;
            updateSubmit();
            @endif
        })();
    </script>
@endsection