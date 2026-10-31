@extends('Core::Admin.layouts.app')
@section('title', 'Yêu cầu rút tiền')
@section('page-title', 'Rút tiền')
@section('content')

    @if(session('success'))
        <div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:18px;color:#15803d;font-size:13px;font-weight:600">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <style>
        .withdraw-note {
            margin-bottom: 18px;
        }

        .withdraw-note ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .withdraw-note ul li {
            position: relative;
            background: linear-gradient(135deg, #f8fafc, #eef6ff);
            border: 1px solid #dbeafe;
            border-left: 4px solid #2563eb;
            border-radius: 12px;
            padding: 12px 14px 12px 45px;
            font-size: 13px;
            line-height: 1.6;
            color: #334155;
            font-weight: 500;
            box-shadow: 0 3px 10px rgba(37, 99, 235, 0.05);
            transition: all .2s ease;
        }

        .withdraw-note ul li:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.12);
        }

        .withdraw-note ul li::before {
            content: "✓";
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #2563eb;
            color: #fff;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .withdraw-note ul li strong {
            color: #1e40af;
        }
    </style>

    {{-- FORM YÊU CẦU RÚT --}}
    <div class="card" style="margin-bottom:22px">
        <div class="card-header">
            <h5 style="margin:0"><i class="fas fa-paper-plane"></i> Tạo yêu cầu rút tiền mới</h5>
        </div>
        <div class="card-body">
            <div style="background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:9px;padding:10px 16px;margin-bottom:18px;font-size:13px;color:#1d4ed8">
                <i class="fas fa-wallet"></i> Số dư hiện tại:
                <strong>{{ number_format($user->vi_tien ?? 0, 0, ',', '.') }}₫</strong>
            </div>

            @if($errors->any())
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:9px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#dc2626">
                    @foreach($errors->all() as $e)
                        <div>• {{ $e }}</div>
                    @endforeach
                </div>
            @endif
            <div class="withdraw-note">
                <ul>
                    <li>
                        Tên chủ tài khoản phải viết <strong>IN HOA KHÔNG DẤU</strong>
                    </li>

                    <li>
                        Nếu là ngân hàng ngoài ACB hãy tích vào
                        <strong>“Ngân hàng ngoài ACB”</strong>
                        để chọn chi nhánh và thành phố
                    </li>
                </ul>
            </div>
            <form method="POST" action="{{ route('admin.my-withdrawals.store') }}">
                @csrf
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Chọn tên ngân hàng *</label>
                            <select name="ngan_hang" id="ten_ngan_hang" class="form-control">
                                <option value="">-- Chọn tên ngân hàng --</option>
                                @foreach($tenNganHangs as $tenNganHang)
                                    <option value="{{ $tenNganHang->name }}" data-ten-viet-tat="{{ $tenNganHang->ten_viet_tat }}"
                                            {{ old('ngan_hang', $userTongNganHangName) == $tenNganHang->name ? 'selected' : '' }}>
                                        {{ $tenNganHang->name }} ({{ $tenNganHang->ten_viet_tat }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nhập số tiền cần rút *</label>
                            <input type="number" name="so_tien" class="form-control" value="{{ old('so_tien') }}"
                                   placeholder="Tối thiểu 500,000₫" min="500000" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Chọn thành phố *</label>
                            <select name="thanh_pho_id" id="thanh_pho" class="form-control">
                                <option value="">-- Chọn thành phố --</option>
                                @foreach($thanhPho as $tp)
                                    <option value="{{ $tp->id }}" {{ old('thanh_pho_id', $userBankCityId) == $tp->id ? 'selected' : '' }}>{{ $tp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Chọn chi nhánh ngân hàng</label>
                            <select name="ngan_hang_id" id="ngan_hang" class="form-control">
                                <option value="">-- Chọn chi nhánh ngân hàng --</option>
                                @foreach($nganHangs as $nh)
                                    <option value="{{ $nh->id }}" data-city="{{ $nh->thanh_pho_id }}"
                                            {{ old('ngan_hang_id', $user->ngan_hang_id) == $nh->id ? 'selected' : '' }}>
                                        {{ $nh->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Số tài khoản *</label>
                            <input type="text" name="stk" class="form-control" value="{{ old('stk', $user->stk) }}"
                                   placeholder="Số tài khoản" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tên chủ tài khoản <span style="color: red; text-transform:uppercase">(Viết chữ in hoa không dấu)</span>
                                *</label>
                            <input type="text" name="chu_tk" class="form-control" value="{{ old('chu_tk', $user->chu_tk) }}"
                                   placeholder="Đúng tên trong ngân hàng" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Căn cước công dân *</label>
                            <input type="text" name="cccd" class="form-control" value="{{ old('cccd', $user->cccd) }}"
                                   placeholder="Viết đầy đủ số căn cước công dân" required readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="myCheckbox">
                                <input type="checkbox" id="myCheckbox" name="ACB_Checkbox" value="yes">
                                Ngân hàng ngoài ACB <span
                                        style="color:red">(Tích vào đây nếu là ngân hàng ngoài ACB)</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mã pin *</label>
                            <input type="password" name="pin" class="form-control"
                                   placeholder="Vui lòng nhập mã pin" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>Ghi chú</label>
                            <textarea name="note" class="form-control" rows="2"
                                      placeholder="Ghi chú thêm (tuỳ chọn)">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Gửi yêu cầu rút tiền
                </button>
            </form>
        </div>
    </div>

    {{-- DANH SÁCH YÊU CẦU --}}
    <div class="card">
        <div class="card-header"><h5 style="margin:0"><i class="fas fa-list"></i> Lịch sử yêu cầu rút tiền</h5></div>
        <div class="card-body" style="padding:0">
            @if($items->count())
                <table class="table" style="margin:0">
                    <thead>
                    <tr>
                        <th>Ngày</th>
                        <th>Số điểm rút</th>
                        <th>Số điểm nhận</th>
                        <th>Ngân hàng</th>
                        <th>Chi nhánh ngân hàng</th>
                        <th>Số TK</th>
                        <th>Trạng thái</th>
                        <th>Ghi chú</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        @php
                            $status = match((int)$item->status) {
                                0 => ['Chờ duyệt', 'badge-members'],
                                1 => ['Đã duyệt', 'badge-active'],
                                2 => ['Từ chối', 'badge-locked'],
                                default => ['?', ''],
                            };
                        @endphp
                        <tr>
                            <td style="font-size:12px;color:#8a9bb0;white-space:nowrap">{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                            <td style="font-weight:700;color:#dc2626;white-space:nowrap">{{ number_format($item->so_tien, 0, ',', '.') }}
                                ₫
                            </td>

                            @php
                                $so_tien_rut= $item->so_tien ?? 0;
                                $so_tien_thue = $item->so_tien_sau_thue ?? 0;
                                $so_tien_tra = $so_tien_rut-$so_tien_thue;

                            @endphp

                            <td style="font-weight:700;color:#dc2626;white-space:nowrap">{{ number_format($so_tien_tra, 0, ',', '.') }}
                                ₫
                            </td>
                            <td style="font-weight:700;white-space:nowrap">{{  $item->ngan_hang}}</td>
                            <td style="font-size:13px">

                                {{ $item->nganHang->name ?? '' }}
                                <br>
                                <small style="color:#888">
                                    {{ $item->nganHang->thanhPho->name ?? '' }}
                                </small>


                            </td>
                            <td style="font-size:13px;font-family:monospace">{{ $item->stk }}</td>
                            <td><span class="{{ $status[1] }}"
                                      style="padding:2px 10px;border-radius:6px;font-size:11px;font-weight:700">{{ $status[0] }}</span>
                            </td>
                            <td style="font-size:12px;color:#8a9bb0">{{ $item->note }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div style="padding:16px">{{ $items->links() }}</div>
            @else
                <div style="text-align:center;padding:48px;color:#8a9bb0">
                    <i class="fas fa-hand-holding-usd"
                       style="font-size:36px;display:block;margin-bottom:10px;opacity:.3"></i>
                    Chưa có yêu cầu rút tiền nào.
                </div>
            @endif
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const cityEl = document.querySelector('#thanh_pho');
            const bankEl = document.querySelector('#ngan_hang');
            const tenBankEl = document.querySelector('#ten_ngan_hang');

            const originalOptions = [...bankEl.options].map(opt => ({
                value: opt.value,
                text: opt.text,
                city: opt.dataset.city || ""
            }));

            // init TomSelect
            const citySelect = new TomSelect(cityEl, {
                create: false,
                sortField: {field: "text", direction: "asc"}
            });
            const tenBankSelect = new TomSelect(tenBankEl, {
                create: false,

                valueField: 'value',
                labelField: 'text',

                searchField: ['text', 'ten_viet_tat'],

                options: [...tenBankEl.options].map(opt => ({
                    value: opt.value,
                    text: opt.text,
                    ten_viet_tat: opt.dataset.tenVietTat || ''
                })),

                render: {
                    option: function(data, escape) {
                        return `
                        <div style="padding:8px 10px">
                            <div style="font-weight:600">
                                ${escape(data.text)}
                            </div>
                            <small style="color:#64748b">
                                ${escape(data.ten_viet_tat)}
                            </small>
                        </div>
                    `;
                    }
                }
            });

            // Chi nhánh ngân hàng
            const bankSelect = new TomSelect(bankEl, {
                create: false,
                sortField: {field: "text", direction: "asc"}
            });


            citySelect.on("change", function (value) {

                // reset
                bankSelect.clear();
                bankSelect.clearOptions();

                // filter lại danh sách ngân hàng
                const filtered = originalOptions.filter(opt => {
                    return !opt.city || opt.city == String(value);
                });

                // add lại option
                filtered.forEach(opt => {
                    bankSelect.addOption({
                        value: opt.value,
                        text: opt.text
                    });
                });

                // render lại
                bankSelect.refreshOptions(false);
            });

        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Thành công',
                text: "{{ session('success') }}",
                confirmButtonText: 'Đóng'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Thông báo',
                text: "{{ session('error') }}",
                confirmButtonText: 'Đóng'
            });
        </script>
    @endif

@endsection
