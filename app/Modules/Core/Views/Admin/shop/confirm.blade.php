@extends('Core::Admin.layouts.app')
@section('title', 'Kiem tra lai don hang')
@section('page-title', 'Kiem tra lai don hang')

@push('styles')
    <style>
        .confirm-wrap {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 20px;
            align-items: start
        }

        .confirm-panel {
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px
        }

        .confirm-title {
            font-size: 14px;
            font-weight: 800;
            color: #1e2a3a;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px
        }

        .confirm-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            overflow: hidden
        }

        .confirm-table th {
            background: #f8faff;
            color: #8a9bb0;
            font-size: 11px;
            text-transform: uppercase;
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #f1f5f9
        }

        .confirm-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: #1e2a3a;
            vertical-align: middle
        }

        .confirm-table tr:last-child td {
            border-bottom: none
        }

        .confirm-product {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .confirm-thumb {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            background: #f8faff;
            flex-shrink: 0
        }

        .confirm-thumb-placeholder {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8faff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            flex-shrink: 0
        }

        .muted {
            color: #8a9bb0;
            font-size: 12px
        }

        .info-list {
            display: grid;
            gap: 10px
        }

        .info-row {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 8px;
            font-size: 13px;
            color: #1e2a3a
        }

        .info-row b {
            color: #5a6a7e
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
            font-size: 14px
        }

        .summary-row.total {
            border-top: 1px solid #e2e8f0;
            margin-top: 12px;
            padding-top: 12px;
            font-size: 18px;
            font-weight: 800;
            color: #4e8ef7
        }

        .summary-row.hoan {
            color: #16a34a;
            font-weight: 700
        }

        .btn-confirm {
            width: 100%;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #4e8ef7, #6c63ff);
            color: #fff;
            font-weight: 800;
            padding: 13px;
            cursor: pointer;
            margin-top: 12px
        }

        .btn-confirm:disabled {
            opacity: .55;
            cursor: not-allowed
        }

        .btn-back {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #5a6a7e;
            text-decoration: none;
            font-size: 13px
        }

        .btn-remove {
            border: none;
            background: #fef2f2;
            color: #dc2626;
            border-radius: 8px;
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: .2s
        }

        .btn-remove:hover {
            background: #fee2e2;
            transform: scale(.96)
        }

        .btn-remove svg {
            width: 18px;
            height: 18px
        }

        .qty-control {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            white-space: nowrap
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border: 1.5px solid #e2e8f0;
            border-radius: 6px;
            background: #fff;
            color: #1e2a3a;
            font-size: 14px;
            font-weight: 800;
            line-height: 1;
            cursor: pointer;
            transition: .2s
        }

        .qty-btn:hover {
            border-color: #ff9f43;
            background: #fff7ed;
            color: #c2410c
        }

        .qty-text {
            min-width: 24px;
            text-align: center;
            font-weight: 800;
            color: #1e2a3a
        }

        .empty-selected {
            display: none;
            text-align: center;
            color: #8a9bb0;
            padding: 30px;
            border: 1px dashed #e2e8f0;
            border-radius: 10px;
            margin-top: 12px
        }

        @media(max-width:768px) {
            .confirm-wrap {
                grid-template-columns: 1fr
            }

            .confirm-panel {
                padding: 14px
            }

            .confirm-table th:nth-child(3),
            .confirm-table td:nth-child(3) {
                display: none
            }

            .info-row {
                grid-template-columns: 1fr
            }
        }
    </style>
@endpush

@section('content')
    <div class="confirm-wrap">
        <div class="confirm-panel">
            <div class="confirm-title"><i class="fas fa-list-ul" style="color:#ff9f43"></i> Sản phẩm đã chọn</div>
            <table class="confirm-table">
                <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th style="text-align:center">SL</th>
                    <th style="text-align:right">Đơn giá</th>
                    <th style="text-align:right">Thành tiền</th>
                    <th style="text-align:right">Thao tác</th>

                </tr>
                </thead>
                <tbody>
                @foreach ($items as $item)
                    @php($child = $item['product'])
                    <tr class="confirm-item-row" data-product-id="{{ $child->id }}" data-unit-price="{{ $child->price }}" data-line-total="{{ $item['line_total'] }}">
                        <td>
                            <div class="confirm-product">
                                @if ($child->image)
                                    <img class="confirm-thumb" src="{{ cdn($child->image) }}" alt="{{ $child->name }}">
                                @else
                                    <span class="confirm-thumb-placeholder"><i class="fas fa-box"></i></span>
                                @endif
                                <div>
                                    <b>{{ $child->name }}</b>
                                    <div class="muted">{{ $child->category?->name ?? 'Khong co danh muc' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="text-align:center">
                            <div class="qty-control">
                                <button type="button" class="qty-btn" onclick="changeConfirmQty({{ $child->id }}, -1)">-</button>
                                <span class="qty-text">{{ $item['qty'] }}</span>
                                <button type="button" class="qty-btn" onclick="changeConfirmQty({{ $child->id }}, 1)">+</button>
                            </div>
                        </td>
                        <td style="text-align:right">{{ number_format($child->price, 0, ',', '.') }}</td>
                        <td style="text-align:right" class="line-total-text">{{ number_format($item['line_total'], 0, ',', '.') }}</td>
                        <td style="text-align:right">
                            <button type="button" class="btn-remove" onclick="removeConfirmItem({{ $child->id }})" title="Xoa san pham">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18"></path>
                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                </svg>
                            </button>
                        </td>


                    </tr>
                @endforeach
                </tbody>
            </table>
            <div id="empty-selected" class="empty-selected">
                Chua con san pham nao trong don. Vui long quay lai chon san pham.
            </div>
        </div>

        <div class="confirm-panel">
            <div class="confirm-title"><i class="fas fa-user" style="color:#4e8ef7"></i> Thông tin nhận hàng</div>
            <div class="info-list">
                <div class="info-row"><b>Người nhận</b><span>{{ $data['receive_user'] }}</span></div>
                <div class="info-row"><b>Điện thoại</b><span>{{ $data['receive_phone'] }}</span></div>
                <div class="info-row"><b>Tỉnh/TP</b><span>{{ $province?->name ?? $data['province_id'] }}</span></div>
                <div class="info-row"><b>Quận/Huyện</b><span>{{ $district?->name ?? $data['district_id'] }}</span></div>
                <div class="info-row"><b>Phường/Xã</b><span>{{ $ward?->name ?? $data['ward_id'] }}</span></div>
                <div class="info-row"><b>Địa chỉ</b><span>{{ $data['receive_address'] }}</span></div>
                @if (!empty($data['note']))
                    <div class="info-row"><b>Ghi chú</b><span>{{ $data['note'] }}</span></div>
                @endif
            </div>

            <div style="height:1px;background:#f1f5f9;margin:16px 0"></div>
            @php($payableTotal = $payableTotal ?? ($total + ($shippingFee ?? 54000)))
            <div class="summary-row"><span>Tổng tiền hàng</span><b>{{ number_format($total, 0, ',', '.') }}</b></div>
            <div class="summary-row hoan"><span>Hoàn dự kiến</span><b>{{ number_format($totalHoan, 0, ',', '.') }}</b>
            </div>
            <div class="summary-row"><span>Ví mua hàng hiện có</span><b>{{ number_format($wallet, 0, ',', '.') }}</b></div>
            <div class="summary-row shipping"><span>Phi ship</span><b>{{ number_format($shippingFee ?? 54000, 0, ',', '.') }}</b></div>
            <div class="summary-row total"><span>Thanh toán</span><span>{{ number_format($payableTotal, 0, ',', '.') }}</span>
            </div>

            <form method="POST" action="{{ $paymentAction }}" id="confirm-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="mode" value="{{ old('mode', $data['mode'] ?? 'custom') }}">
                <input type="hidden" name="receive_user" value="{{ $data['receive_user'] }}">
                <input type="hidden" name="receive_phone" value="{{ $data['receive_phone'] }}">
                <input type="hidden" name="province_id" value="{{ $data['province_id'] }}">
                <input type="hidden" name="district_id" value="{{ $data['district_id'] }}">
                <input type="hidden" name="ward_id" value="{{ $data['ward_id'] }}">
                <input type="hidden" name="receive_address" value="{{ $data['receive_address'] }}">
                <input type="hidden" name="note" value="{{ $data['note'] ?? '' }}">
                <input type="hidden" name="confirmed" value="1">
                @foreach ($items as $item)
                    <div class="hidden-item" data-product-id="{{ $item['product']->id }}">
                        <input type="hidden" name="child_ids[]" value="{{ $item['product']->id }}">
                        <input type="hidden" name="child_qtys[]" value="{{ $item['qty'] }}">
                    </div>
                @endforeach
                <button type="submit" class="btn-confirm" id="btn-final-confirm">
                    <i class="fas fa-check-circle"></i>
                    <span id="btn-final-text">{{ $wallet >= $payableTotal ? 'Xac nhan mua bang vi' : 'Xac nhan va nap tien thanh toan' }}</span>
                </button>
            </form>
            <a href="javascript:history.back()" class="btn-back"><i class="fas fa-arrow-left"></i> Quay lai chinh sua</a>
        </div>
    </div>
    <script>
        const walletAmount = {{ (int) $wallet }};
        const shippingFee = {{ (int) ($shippingFee ?? 54000) }};
        const orderStoreAction = "{{ route('admin.orders.store') }}";
        const payosAction = "{{ route('payos.create-payment-link') }}";

        function formatVN(value) {
            return new Intl.NumberFormat('vi-VN').format(value);
        }

        function removeConfirmItem(productId) {
            const row = document.querySelector(`.confirm-item-row[data-product-id="${productId}"]`);
            const hidden = document.querySelector(`.hidden-item[data-product-id="${productId}"]`);

            if (row) row.remove();
            if (hidden) hidden.remove();

            updateConfirmSummary();
        }

        function changeConfirmQty(productId, step) {
            const row = document.querySelector(`.confirm-item-row[data-product-id="${productId}"]`);
            const hidden = document.querySelector(`.hidden-item[data-product-id="${productId}"]`);
            if (!row || !hidden) return;

            const qtyText = row.querySelector('.qty-text');
            const qtyInput = hidden.querySelector('input[name="child_qtys[]"]');
            const unitPrice = parseInt(row.dataset.unitPrice || '0', 10) || 0;
            const currentQty = parseInt(qtyText.textContent || '1', 10) || 1;
            const nextQty = Math.max(1, currentQty + step);
            const lineTotal = unitPrice * nextQty;

            qtyText.textContent = nextQty;
            qtyInput.value = nextQty;
            row.dataset.lineTotal = lineTotal;
            row.querySelector('.line-total-text').textContent = formatVN(lineTotal);

            updateConfirmSummary();
        }

        function updateConfirmSummary() {
            let total = 0;
            const rows = document.querySelectorAll('.confirm-item-row');

            rows.forEach(row => {
                total += parseInt(row.dataset.lineTotal || '0', 10) || 0;
            });

            const payableTotal = total + shippingFee;
            const totalHoan = Math.max(0, total - shippingFee);
            const summaryRows = document.querySelectorAll('.summary-row');
            const totalText = summaryRows[0]?.querySelector('b');
            const hoanText = document.querySelector('.summary-row.hoan b');
            const payText = document.querySelector('.summary-row.total span:last-child');

            if (totalText) totalText.textContent = formatVN(total);
            if (hoanText) hoanText.textContent = formatVN(totalHoan);
            if (payText) payText.textContent = formatVN(payableTotal);

            const form = document.getElementById('confirm-form');
            const button = document.getElementById('btn-final-confirm');
            const buttonText = document.getElementById('btn-final-text');
            const emptyBox = document.getElementById('empty-selected');

            if (rows.length === 0) {
                button.disabled = true;
                buttonText.textContent = 'Vui long chon lai san pham';
                emptyBox.style.display = 'block';
                return;
            }

            emptyBox.style.display = 'none';
            button.disabled = false;

            if (walletAmount >= payableTotal) {
                form.action = orderStoreAction;
                buttonText.textContent = 'Xac nhan mua bang vi';
            } else {
                form.action = payosAction;
                buttonText.textContent = 'Xac nhan va nap tien thanh toan';
            }
        }
    </script>
@endsection
