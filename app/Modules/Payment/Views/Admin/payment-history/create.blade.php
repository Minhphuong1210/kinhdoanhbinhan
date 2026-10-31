@extends('Core::Admin.layouts.app')
@section('title', 'Nạp tiền')
@section('page-title', 'Nạp tiền qua PayOS')

@section('content')

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div style="max-width:520px;margin:0 auto;">
    <div class="card" style="border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);">
        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#3b82f6,#2563eb);padding:28px 28px 24px;">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:52px;height:52px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-wallet" style="font-size:22px;color:#fff;"></i>
                </div>
                <div>
                    <h4 style="color:#fff;margin:0;font-size:18px;font-weight:700;">Nạp tiền vào ví</h4>
                    <p style="color:rgba(255,255,255,.75);margin:0;font-size:13px;">Thanh toán nhanh qua QR PayOS</p>
                </div>
            </div>
        </div>

        {{-- Số dư hiện tại --}}
        <div style="background:#eff6ff;padding:14px 28px;border-bottom:1px solid #dbeafe;">
            <span style="font-size:12px;color:#3b82f6;">Số dư hiện tại: </span>
            <strong style="font-size:15px;color:#1e40af;">
                {{ number_format(auth()->user()->vi_tien ?? 0, 0, ',', '.') }}₫
            </strong>
        </div>

        <div class="card-body" style="padding:28px;">
            <form action="{{ route('payos.create-payment-link') }}" method="POST" id="formNapTien">
                @csrf

                {{-- Gói dịch vụ (ẩn, nếu có) --}}
                <input type="hidden" name="goiDichVuId" value="{{ request('goiDichVuId') }}">

                {{-- Số tiền --}}
                <div style="margin-bottom:20px;">
                    <label style="font-weight:600;font-size:13px;color:#374151;display:block;margin-bottom:8px;">
                        Số tiền muốn nạp <span style="color:#dc2626">*</span>
                    </label>
                    <div style="position:relative;">
                        <input type="text" name="soTien" id="soTien" required
                               placeholder="Nhập số tiền..."
                               style="width:100%;padding:12px 48px 12px 14px;border:1.5px solid #d1d5db;border-radius:10px;font-size:16px;font-weight:600;outline:none;transition:border .2s;"
                               onfocus="this.style.borderColor='#3b82f6'"
                               onblur="this.style.borderColor='#d1d5db'"
                               oninput="formatMoney(this)">
                        <span style="position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#6b7280;font-weight:700;">₫</span>
                    </div>
                    <p style="font-size:11px;color:#9ca3af;margin-top:5px;">Tối thiểu: 2,000₫</p>
                </div>

                {{-- Chọn nhanh số tiền --}}
                <div style="margin-bottom:22px;">
                    <label style="font-size:12px;color:#6b7280;display:block;margin-bottom:8px;">Chọn nhanh</label>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        @foreach([50000, 100000, 200000, 500000, 1000000, 2000000] as $amount)
                            <button type="button"
                                    onclick="setAmount({{ $amount }})"
                                    style="padding:6px 14px;border:1.5px solid #d1d5db;border-radius:8px;background:#fff;cursor:pointer;font-size:12px;font-weight:600;color:#374151;transition:all .15s;"
                                    onmouseover="this.style.borderColor='#3b82f6';this.style.color='#3b82f6'"
                                    onmouseout="this.style.borderColor='#d1d5db';this.style.color='#374151'">
                                {{ number_format($amount, 0, ',', '.') }}₫
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Nút submit --}}
                <button type="submit" id="btnSubmit"
                        style="width:100%;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border:none;padding:14px;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;transition:opacity .2s;letter-spacing:.3px;">
                    <i class="fas fa-qrcode me-2"></i>Tạo QR thanh toán
                </button>

                <p style="text-align:center;font-size:11px;color:#9ca3af;margin-top:14px;">
                    Bạn sẽ được chuyển đến trang thanh toán PayOS an toàn
                </p>
            </form>
        </div>
    </div>

    <div style="text-align:center;margin-top:14px;">
        <a href="{{ route('admin.payment-history.index') }}" style="font-size:12px;color:#6b7280;text-decoration:none;">
            <i class="fas fa-history me-1"></i>Xem lịch sử nạp tiền
        </a>
    </div>
</div>

<script>
    function formatMoney(input) {
        let raw = input.value.replace(/[^0-9]/g, '');
        input.value = raw ? Number(raw).toLocaleString('vi-VN') : '';
        // Lưu giá trị thô để gửi đi
        input.dataset.raw = raw;
    }

    function setAmount(amount) {
        const input = document.getElementById('soTien');
        input.value = amount.toLocaleString('vi-VN');
        input.dataset.raw = amount;
    }

    // Trước khi submit, đặt lại giá trị thô
    document.getElementById('formNapTien').addEventListener('submit', function () {
        const input = document.getElementById('soTien');
        input.value = input.dataset.raw || input.value.replace(/[^0-9]/g, '');

        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tạo link...';
    });
</script>

@endsection
