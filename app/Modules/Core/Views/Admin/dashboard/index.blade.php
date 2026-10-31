@extends('Core::Admin.layouts.app')
@section('title', 'Dashboard')
@section('page-title', $isAdmin ? 'Dashboard Quản trị' : 'Trang cá nhân')
@section('content')

    @if ($isAdmin)

        <form method="GET" class="card" style="margin-bottom:20px;padding:15px">
            <div class="row">

                {{-- Lọc theo thời gian --}}
                <div class="col-md-3">
                    <label>Từ ngày</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <div class="col-md-3">
                    <label>Đến ngày</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <div class="col-md-12 text-right">
                    <button class="btn btn-primary">
                        <i class="fas fa-filter"></i> Lọc dữ liệu
                    </button>

                    <a href="{{ url()->current() }}" class="btn btn-secondary">
                        Reset
                    </a>
                </div>
            </div>


        </form>



        {{-- ===== ADMIN: Thống kê hệ thống ===== --}}
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-box"></i></div>
                <div class="stat-info">
                    <div class="value">{{ number_format($stats['products']) }}</div>
                    <div class="label">Sản phẩm</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-info">
                    <div class="value">{{ number_format($stats['orders']) }}</div>
                    <div class="label">Đơn hàng</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <div class="value">{{ number_format($stats['users']) }}</div>
                    <div class="label">Thành viên</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <div class="value">{{ number_format($stats['so_tien_rut_tru_thue']) }}</div>
                    <div class="label">Thế thu nhập cá nhân</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon red"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <div class="value">{{ number_format($stats['10_phan_tram_so_tien']) }}</div>
                    <div class="label">Thế thu nhập cá nhân 10%</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon red"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <div class="value">{{ number_format($stats['1_5_phan_tram_so_tien']) }}</div>
                    <div class="label">Thế thu nhập cá nhân 1,5%</div>
                </div>
            </div>




            <div class="stat-card">
                <div class="stat-icon red"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <div class="value">{{ number_format($stats['tong_tien_thuong_hieu_binh_an']) }}</div>
                    <div class="label">Tồng tiền thương hiệu bình an</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon red"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <div class="value">{{ number_format($stats['so_tien_da_tra_thuc_te']) }}</div>
                    <div class="label">Số tiền đã trả thưc tế</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon red"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <div class="value">{{ number_format($stats['so_tien_thanh_vien_chua_du_3_f1']) }}</div>
                    <div class="label">Số tiền của thành viên chưa đủ 3f1 </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon red"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <div class="value">{{ number_format($stats['thanh_vien_du_3f1_tien_duoi_500K']) }}</div>
                    <div class="label">Thành viên đủ 3f1 tiền dưới 500k </div>
                </div>
            </div>

        </div>


        <div class="d-flex" style="
    display: flex;
    width: 100%;
    justify-content: space-around;
">

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fas fa-box-open"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($stats['total_qty']) }}</div>
                        <div class="label">Đã bán</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($stats['total_buy'], 0, ',', '.') }}₫</div>
                        <div class="label">Tổng tiền mua</div>
                    </div>
                </div>


                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($stats['total_buy_price_vat'], 0, ',', '.') }}₫</div>
                        <div class="label">Tiền VAT</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($stats['total_buy_fee_shipping'], 0, ',', '.') }}₫</div>
                        <div class="label">Phí shipp</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($stats['total_buy_not_VAT'], 0, ',', '.') }}₫</div>
                        <div class="label">Tiền chưa Vat</div>
                    </div>
                </div>


                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-undo"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($stats['total_refund'], 0, ',', '.') }}₫</div>
                        <div class="label">Hoàn phí</div>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="stat-card" style="border: 1px dashed #4e8ef7;background-color:#4e8ef7; color:black ">
                    <div class="stat-icon blue"><i class="fas fa-layer-group"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($stats['tuchon_qty']) }}</div>
                        <div class="label" style="color: black">Combo Đã bán</div>
                    </div>
                </div>

                <div class="stat-card" style="border: 1px dashed #22c55e;    background-color: #22c55e;">
                    <div class="stat-icon green"><i class="fas fa-hand-holding-usd"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($stats['tuchon_buy'], 0, ',', '.') }}₫</div>
                        <div class="label" style="color: black">Combo Tiền mua</div>
                    </div>
                </div>


                <div class="stat-card" style="border: 1px dashed #22c55e;    background-color: #22c55e;">
                    <div class="stat-icon green"><i class="fas fa-hand-holding-usd"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($stats['tuchon_buy_price_vat'], 0, ',', '.') }}₫</div>
                        <div class="label" style="color: black">Tiền VAT</div>

                    </div>
                </div>

                <div class="stat-card" style="border: 1px dashed #22c55e;    background-color: #22c55e;">
                    <div class="stat-icon green"><i class="fas fa-hand-holding-usd"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($stats['tuchon_buy_fee_shipping'], 0, ',', '.') }}₫</div>
                        <div class="label" style="color: black">Phí shipping</div>

                    </div>
                </div>

                <div class="stat-card" style="border: 1px dashed #22c55e;    background-color: #22c55e;">
                    <div class="stat-icon green"><i class="fas fa-hand-holding-usd"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($stats['tuchon_buy_not_VAT'], 0, ',', '.') }}₫</div>
                        <div class="label" style="color: black">Tiền chưa Vat</div>

                    </div>
                </div>


                <div class="stat-card" style="border: 1px dashed #ef4444;background-color: #d1ef44">
                    <div class="stat-icon red"><i class="fas fa-redo-alt"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($stats['tuchon_refund'], 0, ',', '.') }}₫</div>
                        <div class="label" style="color: black">Combo Hoàn phí</div>
                    </div>
                </div>
            </div>


                <?php

                $tong_tien_da_ban = 0;
                $tong_san_pham_da_ban = 0;
                $tong_tien_hoan_phi = 0;

                $tong_tien_vat = 0;
                $tong_tien_chua_vat = 0;
                $tong_phi_van_chuyen = 0;
                $tong_loi_nhuan = $stats['total_profit'] ?? 0;

                $tong_san_pham_da_ban = $stats['total_qty'] + $stats['tuchon_qty'];
                $tong_tien_da_ban = $stats['total_buy'] + $stats['tuchon_buy'];
                $tong_tien_hoan_phi = $stats['tuchon_refund'] + $stats['total_refund'];

                $tong_tien_vat = $stats['total_buy_price_vat'] + $stats['tuchon_buy_price_vat'];
                $tong_tien_chua_vat = $stats['total_buy_not_VAT'] + $stats['tuchon_buy_not_VAT'];
                $tong_phi_van_chuyen = $stats['total_buy_fee_shipping'] + $stats['tuchon_buy_fee_shipping'];

                ?>

            <div class="col-md-3">
                <div class="stat-card" style="border: 1px dashed #4e8ef7;background-color:#97a732fc; color:black ">
                    <div class="stat-icon blue"><i class="fas fa-layer-group"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($tong_san_pham_da_ban) }}</div>
                        <div class="label" style="color: black">Tổng Combo Đã bán</div>
                    </div>
                </div>

                <div class="stat-card" style="border: 1px dashed #22c55e;    background-color: #c56c22;">
                    <div class="stat-icon green"><i class="fas fa-hand-holding-usd"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($tong_tien_da_ban, 0, ',', '.') }}₫</div>
                        <div class="label" style="color: black">Tổng Combo Tiền mua</div>
                    </div>
                </div>



                <div class="stat-card" style="border:1px dashed #0ea5e9;background:#7dd3fc;">
                    <div class="stat-icon blue"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($tong_tien_vat, 0, ',', '.') }}₫</div>
                        <div class="label" style="color:black">Tổng Tiền VAT</div>
                    </div>
                </div>
                <div class="stat-card" style="border:1px dashed #f59e0b;background:#fde68a;">
                    <div class="stat-icon yellow"><i class="fas fa-truck"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($tong_phi_van_chuyen, 0, ',', '.') }}₫</div>
                        <div class="label" style="color:black">Tổng Phí Vận Chuyển</div>
                    </div>
                </div>
                <div class="stat-card" style="border:1px dashed #16a34a;background:#86efac;">
                    <div class="stat-icon green"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($tong_tien_chua_vat, 0, ',', '.') }}₫</div>
                        <div class="label" style="color:black">Tổng Tiền Chưa VAT</div>
                    </div>
                </div>



                <div class="stat-card" style="border: 1px dashed #ef4444;background-color: #db44ef">
                    <div class="stat-icon red"><i class="fas fa-redo-alt"></i></div>
                    <div class="stat-info">
                        <div class="value">{{ number_format($tong_tien_hoan_phi, 0, ',', '.') }}₫</div>
                        <div class="label" style="color: black">Tổng Combo Hoàn phí</div>
                    </div>
                </div>

                <div class="stat-card" style="border:1px dashed #22c55e;background:#bbf7d0;">
                    <div class="stat-icon green"><i class="fas fa-chart-line"></i></div>
                    <div class="stat-info">
                        <div class="value" style="color:{{ $tong_loi_nhuan >= 0 ? '#166534' : '#b91c1c' }}">{{ number_format($tong_loi_nhuan, 0, ',', '.') }} VND</div>
                        <div class="label" style="color:black">Tong loi nhuan</div>
                    </div>
                </div>
            </div>

        </div>




        {{-- Thống kê Combo Tự Chọn --}}



        <div class="card" style="margin-top:20px">
            <div class="card-header">
                <h5>Chào mừng, {{ $user->name }} 👋</h5>
            </div>
            <div class="card-body">
                <p style="color:#8a9bb0;font-size:14px">Trang quản trị hệ thống. Sử dụng menu bên trái để điều hướng.</p>
                <p style="color:#5a6a7e;font-size:13px;margin-top:8px">Doanh thu hoàn tất: <strong
                            style="color:#22c55e">{{ number_format($stats['revenue'], 0, ',', '.') }}₫</strong></p>
            </div>
        </div>

        <div class="card" style="margin-top:24px">
            <div class="card-header">
                <h5><i class="fas fa-calendar-alt"></i> Thống kê theo tháng</h5>
            </div>

            <div class="card-body" style="padding:0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Tháng</th>
                            <th style="text-align:center">Combo đã bán</th>
                            <th style="text-align:right">Tổng tiền mua</th>
                            <th style="text-align:right">Hoàn tiền</th>
                            <th style="text-align:right">Giá vốn</th>
                            <th style="text-align:right">Lợi nhuận</th>
                        </tr>
                        </thead>

                        <tbody>
                        @php
                            $monthlyTotalCombo = $monthlyStats->sum('total_combo');


                            $monthlyTotalBuy = $monthlyStats->sum('total_buy');
                            $monthlyTotalRefund = $monthlyStats->sum('total_refund');
                            $monthlyTotalCost = $monthlyStats->sum('total_cost');
                            $monthlyTotalProfit = $monthlyStats->sum('profit');
                        @endphp




                        @foreach ($monthlyStats as $m)
                            <tr>
                                <td><strong>{{ $m->month }}</strong></td>

                                <td style="text-align:center">
                                    {{ number_format($m->total_combo) }}
                                </td>

                                <td style="text-align:right;color:#22c55e">
                                    {{ number_format($m->total_buy, 0, ',', '.') }}₫
                                </td>

                                <td style="text-align:right;color:#ef4444">
                                    {{ number_format($m->total_refund, 0, ',', '.') }}₫
                                </td>

                                <td style="text-align:right;color:#f59e0b">
                                    {{ number_format($m->total_cost, 0, ',', '.') }}₫
                                </td>

                                <td style="text-align:right;color:{{ $m->profit >= 0 ? '#22c55e' : '#ef4444' }}">
                                    {{ number_format($m->profit, 0, ',', '.') }}₫
                                </td>
                            </tr>
                        @endforeach
                        <tr style="background:#f8faff;font-weight:800">
                            <td><strong>Tổng cộng</strong></td>
                            <td style="text-align:center">{{ number_format($monthlyTotalCombo) }}</td>
                            <td style="text-align:right;color:#22c55e">
                                {{ number_format($monthlyTotalBuy, 0, ',', '.') }}₫</td>
                            <td style="text-align:right;color:#ef4444">
                                {{ number_format($monthlyTotalRefund, 0, ',', '.') }}₫</td>
                            <td style="text-align:right;color:#f59e0b">
                                {{ number_format($monthlyTotalCost, 0, ',', '.') }}₫</td>
                            <td style="text-align:right;color:{{ $monthlyTotalProfit >= 0 ? '#22c55e' : '#ef4444' }}">
                                {{ number_format($monthlyTotalProfit, 0, ',', '.') }}₫</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="row" style="margin-top:24px">
            {{-- TOP TÀI KHOẢN CHI TIẾT --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 style="font-weight:700;margin-bottom:0"><i class="fas fa-trophy" style="color:#ffae00"></i>
                            Top 100 nhiều điểm nhất</h5>
                        <span class="badge bg-soft-warning text-warning">Chi tiết thành viên</span>
                    </div>
                    <div class="card-body" style="padding:0; max-height:600px; overflow-y:auto;">
                        <div class="table-responsive">
                            <table class="table table-hover" style="margin-bottom:0">
                                <thead style="background:#f8faff">
                                <tr>
                                    <th style="padding-left:20px;font-size:11px">Thành viên / Email</th>
                                    <th style="font-size:11px">Số điện thoại</th>
                                    <th style="font-size:11px">Địa chỉ</th>
                                    <th style="text-align:right;padding-right:20px;font-size:11px">Số dư (Điểm)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($topUsers as $u)
                                    <tr>
                                        <td style="padding-left:20px">
                                            <div style="font-weight:700;color:#1e2a3a">{{ $u->name }}</div>
                                            <small style="color:#8a9bb0">{{ $u->email }}</small>
                                        </td>
                                        <td style="vertical-align:middle">{{ $u->tel ?: '---' }}</td>
                                        <td style="vertical-align:middle">
                                            <small
                                                    style="color:#5a6a7e">{{ Str::limit($u->address, 50) ?: '---' }}</small>
                                        </td>
                                        <td style="text-align:right;padding-right:20px;vertical-align:middle">
                                                <span
                                                        style="color:#22c55e;font-weight:800;font-size:15px">{{ number_format($u->vi_tien, 0, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- THỐNG KÊ DOANH THU THEO DANH MỤC & SẢN PHẨM --}}
            <div class="col-12" style="margin-top:24px">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 style="font-weight:700;margin-bottom:0"><i class="fas fa-chart-line"
                                                                       style="color:#4e8ef7"></i> Thống kê bán hàng theo danh mục</h5>
                        <span class="badge bg-soft-primary text-primary">Dữ liệu đơn hàng hoàn tất</span>
                    </div>
                    <div class="card-body" style="padding:0">
                        <div class="table-responsive">
                            <table class="table table-hover" style="margin-bottom:0">
                                <thead style="background:#f8faff">
                                <tr>
                                    <th style="padding-left:20px;font-size:11px">Danh mục</th>
                                    <th style="font-size:11px">Sản phẩm</th>
                                    <th style="text-align:center;font-size:11px">Đã bán</th>
                                    <th style="text-align:right;font-size:11px">Doanh thu</th>
                                    <th style="text-align:right;font-size:11px">Hoàn tiền</th>
                                    <th style="text-align:right;font-size:11px">Giá vốn</th>
                                    <th style="text-align:right;padding-right:20px;font-size:11px">Lợi nhuận</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $currentCat = null;
                                    $categoryTotalQty = $categorySales->sum('total_qty_order');
                                    $categoryTotalRevenue = $categorySales->sum('total_revenue');
                                    $categoryTotalRefund = $categorySales->sum('total_refund');
                                    $categoryTotalCost = $categorySales->sum('total_cost');
                                    $categoryTotalProfit = $categorySales->sum('profit');
                                @endphp
                                @foreach ($categorySales as $item)
                                    <tr>
                                        <td
                                                style="padding-left:20px;vertical-align:middle;font-weight:600;color:#4e8ef7">
                                            @if ($currentCat !== $item->category_name)
                                                {{ $item->category_name ?: 'Không phân loại' }}
                                                @php $currentCat = $item->category_name; @endphp
                                            @endif
                                        </td>
                                        <td style="vertical-align:middle">
                                            <div style="font-weight:500">{{ $item->product_name }}</div>
                                            <small style="display:block;color:#8a9bb0;margin-top:4px">
                                                ID sản phẩm: {{ ($item->product_id ?? null) ?: 'chưa gắn' }}
                                            </small>
                                            @if (!empty($item->refund_transaction_ids))
                                                <small style="display:block;color:#64748b;margin-top:4px">
                                                    ID giao dịch: {{ implode(', ', array_slice($item->refund_transaction_ids, 0, 30)) }}
                                                    @if (count($item->refund_transaction_ids) > 30)
                                                        ...
                                                    @endif
                                                </small>
                                            @endif
                                        </td>
                                        <td style="text-align:center;vertical-align:middle">
                                                <span class="badge"
                                                      style="background:#eef2ff;color:#4e8ef7;font-weight:700">{{ number_format($item->total_qty_order) }}</span>
                                        </td>
                                        <td style="text-align:right;vertical-align:middle">
                                                <span
                                                        style="color:#1e2a3a;font-weight:700">{{ number_format($item->total_revenue, 0, ',', '.') }}₫</span>
                                        </td>
                                        <td style="text-align:right;vertical-align:middle">
                                                <span
                                                        style="color:#22c55e;font-weight:700">{{ number_format($item->total_refund, 0, ',', '.') }}₫</span>
                                        </td>
                                        <td style="text-align:right;vertical-align:middle">
                                                <span
                                                        style="color:#f59e0b;font-weight:700">{{ number_format($item->total_cost, 0, ',', '.') }}₫</span>
                                        </td>
                                        <td style="text-align:right;padding-right:20px;vertical-align:middle">
                                                <span
                                                        style="color:{{ $item->profit >= 0 ? '#22c55e' : '#ef4444' }};font-weight:700">{{ number_format($item->profit, 0, ',', '.') }}₫</span>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr style="background:#f8faff;font-weight:800">
                                    <td style="padding-left:20px">Tổng cộng</td>
                                    <td></td>
                                    <td style="text-align:center">{{ number_format($categoryTotalQty) }}</td>
                                    <td style="text-align:right;color:#1e2a3a">
                                        {{ number_format($categoryTotalRevenue, 0, ',', '.') }}₫</td>
                                    <td style="text-align:right;color:#22c55e">
                                        {{ number_format($categoryTotalRefund, 0, ',', '.') }}₫</td>
                                    <td style="text-align:right;color:#f59e0b">
                                        {{ number_format($categoryTotalCost, 0, ',', '.') }}₫</td>
                                    <td
                                            style="text-align:right;padding-right:20px;color:{{ $categoryTotalProfit >= 0 ? '#22c55e' : '#ef4444' }}">
                                        {{ number_format($categoryTotalProfit, 0, ',', '.') }}₫</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- ===== USER THƯỜNG: Thông tin cá nhân ===== --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <h5><i class="fas fa-user-circle"></i> Xin chào, {{ $user->name }} 👋</h5>
            </div>
            <div class="card-body" style="display:flex;align-items:center;gap:20px">
                @if ($user->image)
                    <img src="{{ cdn($user->image) }}"
                         style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #4e8ef7">
                @else
                    <div
                            style="width:72px;height:72px;border-radius:50%;background:#4e8ef7;display:flex;align-items:center;justify-content:center;font-size:28px;color:#fff;font-weight:700">
                        {{ strtoupper(substr($user->name, 0, 1)) }}</div>
                @endif
                <div>
                    <div style="font-size:18px;font-weight:700;color:#1e2a3a">{{ $user->name }}</div>
                    <div style="color:#8a9bb0;font-size:13px">{{ $user->email }}</div>
                    @if ($user->tel)
                        <div style="color:#8a9bb0;font-size:13px"><i class="fas fa-phone"></i> {{ $user->tel }}</div>
                    @endif
                </div>
                <div style="margin-left:auto;text-align:right">
                    <div style="font-size:22px;font-weight:800;color:#22c55e">
                        {{ number_format($user->vi_tien ?? 0, 0, ',', '.') }}₫</div>
                    <div style="color:#8a9bb0;font-size:12px">Số dư ví</div>
                </div>
            </div>
        </div>

        {{-- LINK GIỚI THIỆU --}}
        @php
            $refParam = $user->tel ?: $user->id;
            $refLink = route('admin.register') . '?ref=' . urlencode($refParam);
        @endphp
        <div class="card" style="margin-bottom:20px">
            <div class="card-body" style="padding:16px 20px">
                <div
                        style="font-size:12px;font-weight:700;color:#8a9bb0;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
                    <i class="fas fa-share-alt" style="color:#4e8ef7"></i> Link giới thiệu của bạn
                </div>
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                    <div style="flex:1;background:#f8faff;border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 14px;font-size:12px;color:#4e8ef7;word-break:break-all;font-family:monospace"
                         id="ref-link-box">{{ $refLink }}</div>
                    <button onclick="copyRefLink()" id="btn-copy-ref"
                            style="background:linear-gradient(135deg,#4e8ef7,#6c63ff);color:#fff;border:none;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:inherit;transition:.2s">
                        <i class="fas fa-copy"></i> Copy link
                    </button>
                </div>
                <div style="font-size:11px;color:#8a9bb0;margin-top:6px"><i class="fas fa-info-circle"></i> Gửi link này
                    cho người muốn đăng ký — SĐT của bạn sẽ tự điền vào ô giới thiệu.</div>
            </div>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-shopping-bag"></i></div>
                <div class="stat-info">
                    <div class="value">{{ $stats['my_orders'] }}</div>
                    <div class="label">Tổng đơn hàng</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <div class="value">{{ $stats['my_pending'] }}</div>
                    <div class="label">Chờ xử lý</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <div class="value">{{ $stats['my_completed'] }}</div>
                    <div class="label">Hoàn tất</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
                <div class="stat-info">
                    <div class="value">{{ $stats['my_cancelled'] }}</div>
                    <div class="label">Đã huỷ</div>
                </div>
            </div>
        </div>

        <div style="margin-top:20px;text-align:center">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> Xem đơn hàng
                của tôi</a>
        </div>
    @endif

    @push('scripts')
        <script>
            function copyRefLink() {
                const link = document.getElementById('ref-link-box').textContent.trim();
                const btn = document.getElementById('btn-copy-ref');
                navigator.clipboard.writeText(link).then(() => {
                    const orig = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i> Đã copy!';
                    btn.style.background = 'linear-gradient(135deg,#22c55e,#16a34a)';
                    setTimeout(() => {
                        btn.innerHTML = orig;
                        btn.style.background = '';
                    }, 2000);
                });
            }
        </script>
    @endpush

@endsection
