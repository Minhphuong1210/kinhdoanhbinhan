@extends('Core::Admin.layouts.app')
@section('title', 'Admin rút tiền từ ví')
@section('page-title', 'Rút tiền từ ví thành viên')
@section('content')

    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <style>
        .withdraw-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(37, 99, 235, 0.08);
            overflow: hidden;
        }
        .withdraw-card .card-header {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: #fff;
            padding: 18px 24px;
            border: none;
        }
        .withdraw-card .card-header h5 {
            margin: 0;
            font-weight: 700;
            font-size: 16px;
        }
        .withdraw-card .card-body {
            padding: 24px;
        }

        .user-info-box {
            background: linear-gradient(135deg, #f0f9ff, #dbeafe);
            border: 1.5px solid #93c5fd;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: none;
            animation: fadeInUp 0.3s ease;
        }
        .user-info-box.show {
            display: block;
        }
        .user-info-box .user-name {
            font-size: 16px;
            font-weight: 700;
            color: #1e40af;
        }
        .user-info-box .user-tel {
            font-size: 13px;
            color: #64748b;
            margin-top: 2px;
        }
        .user-info-box .user-balance {
            font-size: 20px;
            font-weight: 800;
            color: #16a34a;
            margin-top: 8px;
        }
        .user-info-box .user-balance small {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            display: block;
            margin-bottom: 2px;
        }

        .warning-box {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border: 1.5px solid #fbbf24;
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #92400e;
            line-height: 1.6;
        }
        .warning-box i {
            color: #f59e0b;
            margin-right: 6px;
        }

        .form-label-custom {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            display: block;
        }

        .btn-submit {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            border: none;
            color: #fff;
            padding: 12px 32px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.3);
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
            background: linear-gradient(135deg, #b91c1c, #dc2626);
            color: #fff;
        }

        .history-link {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .history-link:hover {
            background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
            color: #1e293b;
            transform: translateY(-1px);
            text-decoration: none;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Warning --}}
            <div class="warning-box">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Lưu ý:</strong> Chức năng này chỉ dành cho Admin. Tiền sẽ bị trừ trực tiếp từ ví thành viên
                và được ghi lại trong lịch sử giao dịch với nội dung Admin thao tác.
            </div>

            {{-- Main form --}}
            <div class="card withdraw-card">
                <div class="card-header">
                    <h5><i class="fas fa-hand-holding-usd"></i> Rút tiền từ ví thành viên</h5>
                </div>
                <div class="card-body">

                    @if($errors->any())
                        <div style="background:#fef2f2;border:1.5px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:#dc2626">
                            @foreach($errors->all() as $e)
                                <div>• {{ $e }}</div>
                            @endforeach
                        </div>
                    @endif

                    {{-- User info box (shown after selection) --}}
                    <div class="user-info-box" id="userInfoBox">
                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
                            <div>
                                <div class="user-name" id="infoName">—</div>
                                <div class="user-tel" id="infoTel">—</div>
                            </div>
                            <div class="user-balance">
                                <small>Số dư ví hiện tại</small>
                                <span id="infoBalance">0₫</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.admin-rut-tien.store') }}" id="withdrawForm">
                        @csrf

                        <div class="row">
                            {{-- Tìm thành viên --}}
                            <div class="col-md-12">
                                <div class="form-group" style="margin-bottom:18px">
                                    <label class="form-label-custom">
                                        <i class="fas fa-search" style="color:#3b82f6;margin-right:4px"></i>
                                        Tìm thành viên (SĐT / ID / Email / Tên) *
                                    </label>
                                    <input type="text" id="searchUser" class="form-control"
                                           placeholder="Nhập để tìm kiếm thành viên..."
                                           autocomplete="off">
                                    <input type="hidden" name="user_id" id="userId" value="{{ old('user_id') }}">
                                </div>
                            </div>

                            {{-- Số tiền --}}
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom:18px">
                                    <label class="form-label-custom">
                                        <i class="fas fa-coins" style="color:#f59e0b;margin-right:4px"></i>
                                        Số tiền cần rút *
                                    </label>
                                    <input type="number" name="so_tien" id="soTien" class="form-control"
                                           value="{{ old('so_tien') }}"
                                           placeholder="Nhập số tiền" min="1000" required>
                                    <small style="color:#64748b;font-size:11px;margin-top:4px;display:block">
                                        Tối thiểu 1.000đ
                                    </small>
                                </div>
                            </div>

                            {{-- Lý do --}}
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom:18px">
                                    <label class="form-label-custom">
                                        <i class="fas fa-pen" style="color:#8b5cf6;margin-right:4px"></i>
                                        Lý do rút tiền *
                                    </label>
                                    <input type="text" name="ly_do" class="form-control"
                                           value="{{ old('ly_do') }}"
                                           placeholder="VD: Thu hồi tiền vi phạm, điều chỉnh số dư..."
                                           required>
                                </div>
                            </div>
                        </div>

                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-top:8px">
                            <button type="submit" class="btn btn-submit" id="btnSubmit">
                                <i class="fas fa-minus-circle"></i> Xác nhận rút tiền
                            </button>
                            <a href="{{ route('admin.admin-rut-tien.history') }}" class="history-link">
                                <i class="fas fa-history"></i> Xem lịch sử rút
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const searchInput = document.getElementById('searchUser');
            const userIdInput = document.getElementById('userId');
            const infoBox     = document.getElementById('userInfoBox');
            const infoName    = document.getElementById('infoName');
            const infoTel     = document.getElementById('infoTel');
            const infoBalance = document.getElementById('infoBalance');
            const form        = document.getElementById('withdrawForm');

            let debounceTimer = null;
            let selectedUserBalance = 0;

            // AJAX search user
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                const q = this.value.trim();

                if (q.length < 2) {
                    infoBox.classList.remove('show');
                    userIdInput.value = '';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('admin.admin-rut-tien.lookup') }}?q=${encodeURIComponent(q)}`)
                        .then(r => r.json())
                        .then(data => {
                            if (data.found) {
                                userIdInput.value = data.id;
                                infoName.textContent = data.name + ' (ID: ' + data.id + ')';
                                infoTel.textContent = (data.tel || '') + (data.email ? ' — ' + data.email : '');
                                infoBalance.textContent = data.vi_tien + '₫';
                                selectedUserBalance = data.vi_tien_raw;
                                infoBox.classList.add('show');
                            } else {
                                infoBox.classList.remove('show');
                                userIdInput.value = '';
                                selectedUserBalance = 0;
                            }
                        })
                        .catch(() => {
                            infoBox.classList.remove('show');
                            userIdInput.value = '';
                        });
                }, 350);
            });

            // Confirm before submit
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                if (!userIdInput.value) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Chưa chọn thành viên',
                        text: 'Vui lòng tìm và chọn thành viên trước khi rút tiền.',
                        confirmButtonText: 'Đã hiểu'
                    });
                    return;
                }

                const soTien = parseFloat(document.getElementById('soTien').value) || 0;
                if (soTien > selectedUserBalance) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Số dư không đủ',
                        text: 'Thành viên chỉ có ' + new Intl.NumberFormat('vi-VN').format(selectedUserBalance) + 'đ trong ví.',
                        confirmButtonText: 'Đóng'
                    });
                    return;
                }

                Swal.fire({
                    icon: 'warning',
                    title: 'Xác nhận rút tiền',
                    html: `Bạn chắc chắn muốn rút <strong style="color:#dc2626">${new Intl.NumberFormat('vi-VN').format(soTien)}₫</strong> từ ví của <strong>${infoName.textContent}</strong>?`,
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Xác nhận rút tiền',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: "{{ session('success') }}",
                    confirmButtonText: 'Đóng'
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: "{{ session('error') }}",
                    confirmButtonText: 'Đóng'
                });
            });
        </script>
    @endif

@endsection
