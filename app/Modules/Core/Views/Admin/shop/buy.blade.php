@extends('Core::Admin.layouts.app')
@section('title', 'Đặt mua: ' . $product->name)
@section('page-title', 'Đặt hàng')
@push('styles')
    <style>
        .order-wrap {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 24px;
            align-items: flex-start
        }

        .product-card {
            background: #fff;
            border-radius: 14px;
            border: 1.5px solid #e2e8f0;
            overflow: hidden;
            position: sticky;
            top: 20px
        }

        .product-card .prod-img {
            width: 100%;
        }

        .product-card .prod-img-placeholder {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #4e8ef7, #6c63ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: rgba(255, 255, 255, .4)
        }

        .product-card .prod-body {
            padding: 18px
        }

        .product-card .prod-cat {
            font-size: 11px;
            color: #4e8ef7;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 6px
        }

        .product-card .prod-name {
            font-size: 16px;
            font-weight: 700;
            color: #1e2a3a;
            line-height: 1.4;
            margin-bottom: 12px
        }

        .price-now {
            font-size: 26px;
            font-weight: 800;
            color: #4e8ef7;
            margin-bottom: 2px
        }

        .price-old {
            font-size: 13px;
            color: #adb5bd;
            text-decoration: line-through;
            margin-bottom: 8px
        }

        .hoan-badge {
            background: #dcfce7;
            color: #16a34a;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 14px
        }

        .discount-badge {
            background: #fee2e2;
            color: #dc2626;
            padding: 3px 9px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 700;
            margin-left: 8px
        }

        .desc-text {
            font-size: 13px;
            color: #5a6a7e;
            line-height: 1.6;
            border-top: 1px solid #f1f5f9;
            padding-top: 14px;
            margin-top: 4px
        }

        .order-form {
            background: #fff;
            border-radius: 14px;
            border: 1.5px solid #e2e8f0;
            padding: 24px
        }

        .section-head {
            font-size: 12px;
            font-weight: 700;
            color: red;
            text-transform: uppercase;
            letter-spacing: .7px;
            margin: 20px 0 14px;
            display: flex;
            align-items: center;
            gap: 8px
        }

        .section-head::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #f1f5f9
        }

        .section-head:first-child {
            margin-top: 0
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px
        }

        .form-row.full {
            grid-template-columns: 1fr
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px
        }

        .form-group label {
            font-size: 12px;
            font-weight: 600;
            color: #374151
        }

        .input-wrap {
            position: relative
        }

        .input-wrap i {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 12px;
            pointer-events: none
        }

        .form-control {
            width: 100%;
            padding: 9px 14px 9px 33px;
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            font-size: 13px;
            font-family: inherit;
            color: #1e2a3a;
            outline: none;
            transition: .2s;
            background: #fff
        }

        .form-control:focus {
            border-color: #4e8ef7;
            box-shadow: 0 0 0 3px rgba(78, 142, 247, .1)
        }

        .form-control.is-invalid {
            border-color: #ef4444
        }

        .err-msg {
            font-size: 11px;
            color: #ef4444
        }

        .confirm-box {
            background: #fffbeb;
            border: 1.5px solid #fde68a;
            border-radius: 10px;
            padding: 14px 16px;
            margin-top: 20px;
            display: flex;
            gap: 12px;
            align-items: flex-start
        }

        .confirm-box input[type=checkbox] {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            margin-top: 2px;
            accent-color: #4e8ef7;
            cursor: pointer
        }

        .confirm-box label {
            font-size: 13px;
            color: #92400e;
            line-height: 1.5;
            cursor: pointer
        }

        .confirm-box label b {
            color: #78350f
        }

        .btn-order {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #4e8ef7, #6c63ff);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: .2s;
            margin-top: 18px
        }

        .btn-order:hover {
            opacity: .9;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(78, 142, 247, .3)
        }

        .btn-order:disabled {
            opacity: .5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none
        }

        .alert-err {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 9px;
            padding: 10px 14px;
            font-size: 13px;
            color: #dc2626;
            margin-bottom: 16px
        }

        .child-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 10px;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            overflow: hidden
        }

        .child-table th {
            background: #f8faff;
            padding: 10px;
            font-size: 11px;
            color: #8a9bb0;
            text-transform: uppercase;
            text-align: left;
            border-bottom: 1px solid #f1f5f9
        }

        .child-table td {
            padding: 12px 10px;
            font-size: 13px;
            color: #1e2a3a;
            border-bottom: 1px solid #f1f5f9
        }

        .child-table tr:last-child td {
            border-bottom: none
        }

        .custom-cat-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 4px 0 12px
        }

        .custom-cat-tab {
            border: 1.5px solid #e2e8f0;
            background: #fff;
            color: #5a6a7e;
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 21px;
            font-weight: 700;
            cursor: pointer;
            transition: .2s
        }

        .custom-cat-tab:hover,
        .custom-cat-tab.active {
            border-color: #ff9f43;
            background: #fff7ed;
            color: #c2410c
        }

        .custom-cat-name {
            display: block;
            font-size: 11px;
            color: #8a9bb0;
            margin-top: 3px
        }

        .custom-empty-row {
            display: none
        }

        .custom-product-main {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .custom-product-toggle {
            border: none;
            background: none;
            padding: 0;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 14px;
            cursor: pointer;
            color: inherit;
            font-family: inherit
        }

        .custom-product-thumb {
            width: 88px;
            height: 88px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            background: #f8faff;
            flex-shrink: 0
        }

        .custom-product-thumb-placeholder {
            width: 88px;
            height: 88px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8faff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            flex-shrink: 0
        }

        .product-modal {
            position: fixed;
            inset: 0;
            z-index: 1050;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            background: rgba(15, 23, 42, .66);
            backdrop-filter: blur(3px)
        }

        .product-modal.open {
            display: flex
        }

        .product-modal-dialog {
            width: min(860px, 100%);
            max-height: min(760px, 92vh);
            overflow: auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 28px 80px rgba(15, 23, 42, .32)
        }

        .product-modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 18px 20px;
            border-bottom: 1px solid #f1f5f9
        }

        .product-modal-title {
            margin: 0;
            color: #1e2a3a;
            font-size: 19px;
            font-weight: 800;
            line-height: 1.35
        }

        .product-modal-close {
            width: 34px;
            height: 34px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            color: #5a6a7e;
            cursor: pointer;
            flex-shrink: 0
        }

        .product-modal-close:hover {
            background: #f8faff;
            color: #dc2626
        }

        .product-modal-body {
            display: grid;
            grid-template-columns: minmax(260px, 42%) 1fr;
            gap: 20px;
            padding: 20px
        }

        .product-modal-media {
            width: 100%;
            aspect-ratio: 1 / 1;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            background: #f8faff;
            align-self: start
        }

        .product-modal-media img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block
        }

        .product-modal-placeholder {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: 42px
        }

        .product-modal-info {
            min-width: 0
        }

        .product-modal-meta {
            display: grid;
            gap: 10px;
            color: #5a6a7e;
            font-size: 13px;
            line-height: 1.55
        }

        .product-modal-meta b {
            color: #1e2a3a
        }

        .product-modal-price {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            margin-bottom: 12px;
            padding: 8px 12px;
            border-radius: 10px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 20px;
            font-weight: 900
        }

        .product-modal-row {
            display: grid;
            grid-template-columns: 86px 1fr;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9
        }

        .product-modal-desc {
            margin-top: 14px;
            padding: 12px;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            background: #fbfdff;
            color: #374151;
            font-size: 13px;
            line-height: 1.65
        }

        .product-modal-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 16px;
            padding: 12px;
            border-radius: 10px;
            background: #fff7ed;
            border: 1px solid #fed7aa
        }

        .modal-qty-label {
            color: #9a3412;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase
        }

        .modal-qty-value {
            min-width: 30px;
            text-align: center;
            color: #1e2a3a;
            font-weight: 900
        }

        .modal-qty-control {
            display: inline-flex;
            align-items: center;
            gap: 7px
        }

        .qty-input {
            width: 60px;
            padding: 5px;
            border: 1.5px solid #e2e8f0;
            border-radius: 6px;
            text-align: center;
            font-size: 13px;
            font-family: inherit
        }

        .qty-control {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            white-space: nowrap
        }

        .qty-btn {
            width: 30px;
            height: 30px;
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

        .summary-box {
            background: #f8faff;
            border: 1.5px solid #4e8ef7;
            border-radius: 10px;
            padding: 16px;
            margin-top: 20px
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px
        }

        .summary-row.total {
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            margin-top: 5px;
            font-weight: 800;
            font-size: 18px;
            color: #4e8ef7
        }

        .summary-row.hoan {
            color: #16a34a;
            font-weight: 700
        }

        @media(max-width:768px) {
            .order-wrap {
                grid-template-columns: 1fr
            }

            .product-card {
                position: static
            }

            .form-row {
                grid-template-columns: 1fr
            }

            .custom-cat-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                padding-bottom: 6px;
                margin-left: -4px;
                margin-right: -4px;
                scroll-snap-type: x proximity
            }

            .custom-cat-tab {
                white-space: nowrap;
                flex: 0 0 auto;
                scroll-snap-align: start
            }

            .child-table {
                font-size: 12px
            }

            .child-table td {
                padding: 10px 8px
            }

            .custom-product-thumb,
            .custom-product-thumb-placeholder {
                width: 72px;
                height: 72px
            }

            .product-modal {
                padding: 12px
            }

            .product-modal-head,
            .product-modal-body {
                padding: 14px
            }

            .product-modal-body {
                grid-template-columns: 1fr;
                gap: 14px
            }

            .product-modal-media {
                aspect-ratio: 16 / 10
            }

            .product-modal-actions {
                align-items: stretch;
                flex-direction: column
            }

            .modal-qty-control {
                justify-content: space-between
            }
        }
    </style>
@endpush
@section('content')

    <div style="margin-bottom:16px">
        <a href="{{ url()->previous(route('admin.shop.index')) }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại
            gói sản phẩm</a>
    </div>

    <div
            style="    padding: 7px 10px 15px;
    background-color: #91e778;
    font-size: 20px;
    font-weight: bold;
    text-transform: uppercase;
    text-align: center;">
        <p>Nếu đặt hàng cho người khác xin điền đầy đủ thông tin người nhận và liên hệ để nhận hàng</p>
    </div>



    @if ($errors->any())
        <div class="alert-err">
            <ul style="list-style:none;padding:0">
                @foreach ($errors->all() as $e)
                    <li>• {{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="order-wrap">

        {{-- === THÔNG TIN SẢN PHẨM === --}}
        <div class="product-card">
            @if ($product->image)
                <img class="prod-img" src="{{ cdn($product->image) }}" alt="{{ $product->name }}">
            @else
                <div class="prod-img-placeholder"><i class="fas fa-box"></i></div>
            @endif
            <div class="prod-body">
                @if ($product->category)
                    <div class="prod-cat">{{ $product->category->name }}</div>
                @endif
                <div class="prod-name">{{ $product->name }}</div>

                @php
                    $discount =
                        $product->old_price && $product->old_price > $product->price
                            ? round((1 - $product->price / $product->old_price) * 100)
                            : 0;
                @endphp

                @if ($product->old_price && $product->old_price > $product->price)
                    <div class="price-old">
                        {{ number_format($product->old_price, 0, ',', '.') }}₫
                        <span class="discount-badge">-{{ $discount }}%</span>
                    </div>
                @endif
                <div class="price-now">{{ number_format($product->price, 0, ',', '.') }}₫</div>

                @if ($product->price_hoan_tien)
                    <div class="hoan-badge"><i class="fas fa-undo"></i> Hoàn
                        {{ number_format($product->price_hoan_tien, 0, ',', '.') }}₫</div>
                @endif

                @if ($product->description)
                    <div class="desc-text">{{ strip_tags($product->description) }}</div>
                @endif
            </div>
        </div>

        {{-- === FORM ĐẶT HÀNG === --}}
        <div class="order-form">
            <form method="POST" action="{{ $isCustom ? route('admin.shop.confirm') : route('admin.orders.store') }}" id="order-form" data-is-custom="{{ $isCustom ? 1 : 0 }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                @if ($isCustom)
                    <input type="hidden" name="mode" value="{{ old('mode', request('mode', 'custom')) }}">
                @endif

                {{-- NGƯỜI NHẬN --}}
                <div class="section-head"><i class="fas fa-user" style="color:#4e8ef7"></i> Thông tin người nhận</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tên người nhận <span style="color:red">*</span></label>
                        <div class="input-wrap"><i class="fas fa-user"></i>
                            <input type="text" name="receive_user"
                                   class="form-control {{ $errors->has('receive_user') ? 'is-invalid' : '' }}"
                                   value="{{ old('receive_user') }}" placeholder="Điền đầy đủ họ và tên" required>
                        </div>
                        @error('receive_user')
                        <div class="err-msg">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại <span style="color:red">*</span></label>
                        <div class="input-wrap"><i class="fas fa-phone"></i>
                            <input type="tel" name="receive_phone"
                                   class="form-control {{ $errors->has('receive_phone') ? 'is-invalid' : '' }}"
                                   value="{{ old('receive_phone') }}" placeholder="0987654321" required>
                        </div>
                        @error('receive_phone')
                        <div class="err-msg">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- ĐỊA CHỈ --}}
                <div class="section-head"><i class="fas fa-map-marker-alt" style="color:#4e8ef7"></i> Địa chỉ nhận hàng
                    (chọn theo danh sách Pancake — tỉnh/TP, quận/huyện, phường/xã) </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tỉnh / Thành phố <span style="color:red">*</span></label>
                        <div class="input-wrap"><i class="fas fa-city"></i>
                            <select name="province_id" id="prov" class="form-control" onchange="loadDist(this.value)"
                                    required>
                                <option value="">— Chọn tỉnh/thành —</option>
                                @foreach ($provinces as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Quận / Huyện <span style="color:red">*</span></label>
                        <div class="input-wrap"><i class="fas fa-map"></i>
                            <select name="district_id" id="dist" class="form-control" onchange="loadWard(this.value)"
                                    required>
                                <option value="">— Chọn quận/huyện —</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-row" style="margin-top:14px">
                    <div class="form-group">
                        <label>Phường / Xã <span style="color:red">*</span></label>
                        <div class="input-wrap"><i class="fas fa-street-view"></i>
                            <select name="ward_id" id="ward" class="form-control" required>
                                <option value="">— Chọn phường/xã —</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ cụ thể <span style="color:red; text-transform: uppercase">(Không cần ghi xã, phường,
                                quận, huyện,... chỉ cần ghi số nhà và thôn xóm) *</span></label>
                        <div class="input-wrap"><i class="fas fa-home"></i>
                            <input type="text" name="receive_address"
                                   class="form-control {{ $errors->has('receive_address') ? 'is-invalid' : '' }}"
                                   value="" placeholder="Ví dụ: Tầng 1, số nhà 3 , ngõ 38, phố Văn La" required>
                        </div>
                        @error('receive_address')
                        <div class="err-msg">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- GHI CHÚ --}}
                <div class="section-head"><i class="fas fa-sticky-note" style="color:#4e8ef7"></i> Ghi chú đơn hàng</div>
                <div class="form-row full">
                    <div class="form-group">
                        <div class="input-wrap"><i class="fas fa-comment" style="top:14px;transform:none"></i>
                            <textarea name="note" class="form-control" rows="2" style="padding-left:33px;resize:vertical"
                                      placeholder="Yêu cầu đặc biệt, thời gian giao hàng...">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- SỐ LƯỢNG / CHỌN SẢN PHẨM --}}
                @if ($isCustom)
                    @php
                        $customCategories = $children
                            ->pluck('category')
                            ->filter()
                            ->unique('id')
                            ->sortBy('name')
                            ->values();
                    @endphp
                    <div class="section-head"><i class="fas fa-list-ul" style="color:#ff9f43"></i> Chọn sản phẩm mua tự
                        chọn</div>
                    @if ($customCategories->count())
                        <div class="custom-cat-tabs">
                            <button type="button" class="custom-cat-tab active" data-cat="all"
                                    onclick="filterCustomCategory('all', this)">TẤT CẢ</button>
                            @foreach ($customCategories as $category)
                                <button type="button" class="custom-cat-tab" data-cat="{{ $category->id }}"
                                        onclick="filterCustomCategory('{{ $category->id }}', this)">
                                    {{ $category->name }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                    <table class="child-table">
                        <thead>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th style="text-align:right">Giá (₫)</th>
                            <th style="text-align:center">Số lượng</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($children as $child)
                            <tr class="custom-product-row" data-cat="{{ $child->category_id ?? 'none' }}">
                                <td>
                                    <button type="button" class="custom-product-toggle"
                                            onclick="openProductModal({{ $child->id }})">
                                        @if ($child->image)
                                            <img class="custom-product-thumb" src="{{ cdn($child->image) }}"
                                                 alt="{{ $child->name }}">
                                        @else
                                            <span class="custom-product-thumb-placeholder"><i
                                                        class="fas fa-box"></i></span>
                                        @endif
                                        <span>
                                                <b>{{ $child->name }}</b>
                                                <span
                                                        class="custom-cat-name">{{ $child->category?->name ?? 'Khong co danh muc' }}</span>
                                            </span>



                                    </button>
                                    <input type="hidden" name="child_ids[]" value="{{ $child->id }}">
                                </td>
                                <td style="text-align:right">{{ number_format($child->price, 0, ',', '.') }} (ĐÃ BAO GỒM VAT) </td>
                                <td style="text-align:center">
                                    <div class="qty-control">
                                        <button type="button" class="qty-btn" onclick="changeCustomQty(this, -1)">-</button>
                                        <input type="number" name="child_qtys[]" class="qty-input child-qty"
                                               data-product-id="{{ $child->id }}" data-price="{{ $child->price }}" value="0" min="0"
                                               max="99" oninput="updateCustomTotal()">
                                        <button type="button" class="qty-btn" onclick="changeCustomQty(this, 1)">+</button>
                                    </div>
                                </td>
                            </tr>
                            <template id="product-modal-data-{{ $child->id }}">
                                <div data-name="{{ e($child->name) }}"
                                     data-category="{{ e($child->category?->name ?? 'Khong co danh muc') }}"
                                     data-price="{{ e(number_format($child->price, 0, ',', '.')) }}"
                                     data-origin="{{ e($child->origin ?? '') }}"
                                     data-image="{{ e($child->image ? cdn($child->image) : '') }}">
                                    <div class="modal-description">
                                        {{ $child->description ? strip_tags($child->description) : '' }}
                                    </div>
                                </div>
                            </template>
                        @endforeach
                        <tr id="custom-empty-row" class="custom-empty-row">
                            <td colspan="3" style="text-align:center;color:#8a9bb0;padding:24px">Khong co san pham
                                trong danh muc nay.</td>
                        </tr>
                        </tbody>
                    </table>
                    <div style="font-size:11px;color:#8a9bb0;margin-top:8px"><i class="fas fa-info-circle"></i> Vui lòng
                        nhập số lượng vào ô tương ứng (vd: nhập 1 để mua). Sản phẩm số lượng 0 sẽ không được tính.</div>
                @else
                    <div class="section-head"><i class="fas fa-boxes" style="color:#4e8ef7"></i> Số lượng</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Số lượng đặt mua</label>
                            <div class="input-wrap"><i class="fas fa-hashtag"></i>
                                <input type="number" name="qty" id="single-qty" class="form-control"
                                       value="{{ old('qty', 1) }}" min="1" max="99"
                                       oninput="updateSingleTotal()" required>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- BẢNG TỔNG KẾT --}}
                <div class="summary-box">
                    <div class="summary-row">
                        <span>Tổng cộng:</span>
                        <span id="display-total">0₫</span>
                    </div>
                    @if ($isCustom)
                        <div class="summary-row">
                            <span>Phi ship:</span>
                            <span id="display-shipping">54.000₫</span>
                        </div>
                    @endif
                    {{--                    <div class="summary-row hoan">--}}
                    {{--                        <span>Tiền hoàn dự kiến (đã bao gồm VAT):</span>--}}
                    {{--                        <span id="display-hoan">0₫</span>--}}
                    {{--                    </div>--}}
                    <div class="summary-row total">
                        <span>Thanh toán:</span>
                        <span id="display-pay">0₫</span>
                    </div>
                </div>

                {{-- XÁC NHẬN --}}
                <div class="confirm-box">
                    <input type="checkbox" id="confirm-check" name="confirmed" value="1"
                           onchange="toggleSubmit(this)" {{ old('confirmed') ? 'checked' : '' }}>
                    <label for="confirm-check">
                        Tôi xác nhận đã kiểm tra và <b>xác nhận thông tin trên là chính xác, không có sai sót</b>. Nếu thông
                        tin sai dẫn đến không nhận được hàng hoặc phát sinh vấn đề, <b>tôi xin chịu hoàn toàn trách
                            nhiệm</b>.
                    </label>
                </div>

                <button type="submit" id="btn-submit" class="btn-order" disabled>
                    <i class="fas fa-check-circle"></i> Xác nhận đặt mua
                </button>
            </form>
        </div>

    </div>

    <div class="product-modal" id="product-modal" onclick="closeProductModal(event)">
        <div class="product-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="product-modal-title"
             onclick="event.stopPropagation()">
            <div class="product-modal-head">
                <h3 class="product-modal-title" id="product-modal-title"></h3>
                <button type="button" class="product-modal-close" onclick="closeProductModal()" aria-label="Dong">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="product-modal-body">
                <div class="product-modal-media" id="product-modal-media"></div>
                <div class="product-modal-info">
                    <div class="product-modal-price" id="product-modal-price"></div>
                    <div class="product-modal-meta">
                        <div class="product-modal-row"><b>Danh mục</b> <span id="product-modal-category"></span></div>
                        <div class="product-modal-row" id="product-modal-origin-row"><b>Xuất xứ</b> <span id="product-modal-origin"></span></div>
                    </div>
                    <div class="product-modal-desc" id="product-modal-desc"></div>
                    <div class="product-modal-actions">
                        <div class="modal-qty-label">Số lượng chọn</div>
                        <div class="modal-qty-control">
                            <button type="button" class="qty-btn" onclick="changeModalQty(-1)">-</button>
                            <span class="modal-qty-value" id="product-modal-qty">0</span>
                            <button type="button" class="qty-btn" onclick="changeModalQty(1)">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // [CŨ - bảng Districts/Wards]
            // const distUrl = "{{ route('admin.ajax.districts', '__P__') }}";
            // const wardUrl = "{{ route('admin.ajax.wards', '__D__') }}";
            // [MỚI - bảng Pancake] dùng cho trang shop/buy
            const distUrl = "{{ route('admin.ajax.pancake_districts', '__P__') }}";
            const wardUrl = "{{ route('admin.ajax.pancake_wards', '__D__') }}";
            const savedDist = "{{ old('district_id', $user->district_id ?? '') }}";
            const savedWard = "{{ old('ward_id', $user->ward_id ?? '') }}";

            function loadDist(pid, selectedDist) {
                const d = document.getElementById('dist'),
                    w = document.getElementById('ward');
                d.innerHTML = '<option value="">— Chọn quận/huyện —</option>';
                w.innerHTML = '<option value="">— Chọn phường/xã —</option>';
                if (!pid) return;
                fetch(distUrl.replace('__P__', pid))
                    .then(r => r.json())
                    .then(list => {
                        list.forEach(i => {
                            // [CŨ] const opt = new Option((i.prefix ? i.prefix + ' ' : '') + i.name, i.id);
                            // [MỚI - Pancake không có prefix]
                            const opt = new Option(i.name, i.id);
                            if (String(i.id) === String(selectedDist || savedDist)) opt.selected = true;
                            d.appendChild(opt);
                        });
                        if (savedDist || selectedDist) loadWard(d.value, savedWard);
                    });
            }

            function loadWard(did, selectedWard) {
                const w = document.getElementById('ward');
                w.innerHTML = '<option value="">— Chọn phường/xã —</option>';
                if (!did) return;
                fetch(wardUrl.replace('__D__', did))
                    .then(r => r.json())
                    .then(list => {
                        list.forEach(i => {
                            // [CŨ] const opt = new Option((i.prefix ? i.prefix + ' ' : '') + i.name, i.id);
                            // [MỚI - Pancake không có prefix]
                            const opt = new Option(i.name, i.id);
                            if (String(i.id) === String(selectedWard || savedWard)) opt.selected = true;
                            w.appendChild(opt);
                        });
                    });
            }

            function toggleSubmit(cb) {
                document.getElementById('btn-submit').disabled = !cb.checked;
            }

            // Auto-load nếu user đã có địa chỉ
            const initProv = document.getElementById('prov').value;
            if (initProv) loadDist(initProv);

            // Restore checkbox state
            const confirmCheck = document.getElementById('confirm-check');
            if (confirmCheck && confirmCheck.checked) {
                document.getElementById('btn-submit').disabled = false;
            }

            // --- REAL-TIME CALCULATION ---
            const isCustom = {{ isset($children) && $children->count() > 0 ? 'true' : 'false' }};
            const basePrice = {{ $product->price }};
            const baseHoan = {{ $product->price_hoan_tien ?? 0 }};
            const shippingFee = {{ $isCustom ? 54000 : 0 }};
            let currentModalProductId = null;

            function formatVN(n) {
                return new Intl.NumberFormat('vi-VN').format(n) + '₫';
            }

            function filterCustomCategory(categoryId, button) {
                document.querySelectorAll('.custom-cat-tab').forEach(tab => tab.classList.remove('active'));
                if (button) button.classList.add('active');

                let visibleCount = 0;
                document.querySelectorAll('.custom-product-row').forEach(row => {
                    const isVisible = categoryId === 'all' || row.dataset.cat === String(categoryId);
                    row.style.display = isVisible ? '' : 'none';
                    if (isVisible) visibleCount++;
                });

                const emptyRow = document.getElementById('custom-empty-row');
                if (emptyRow) {
                    emptyRow.style.display = visibleCount ? 'none' : 'table-row';
                }
            }

            function openProductModal(productId) {
                const template = document.getElementById('product-modal-data-' + productId);
                const modal = document.getElementById('product-modal');
                if (!template || !modal) return;

                currentModalProductId = productId;
                const data = template.content.querySelector('div');
                const desc = template.content.querySelector('.modal-description');
                const image = data.dataset.image || '';
                const origin = data.dataset.origin || '';
                const media = document.getElementById('product-modal-media');
                const originRow = document.getElementById('product-modal-origin-row');
                const qtyInput = document.querySelector(`.child-qty[data-product-id="${productId}"]`);

                document.getElementById('product-modal-title').textContent = data.dataset.name || '';
                document.getElementById('product-modal-category').textContent = data.dataset.category || '';
                document.getElementById('product-modal-price').textContent = (data.dataset.price || '0') + 'đ';
                document.getElementById('product-modal-origin').textContent = origin;
                document.getElementById('product-modal-desc').textContent = desc && desc.textContent.trim()
                    ? desc.textContent.trim()
                    : 'Chưa có mô tả.';
                document.getElementById('product-modal-qty').textContent = qtyInput ? (parseInt(qtyInput.value || '0', 10) || 0) : 0;

                originRow.style.display = origin ? '' : 'none';
                media.innerHTML = '';
                if (image) {
                    const img = document.createElement('img');
                    img.src = image;
                    img.alt = data.dataset.name || '';
                    media.appendChild(img);
                } else {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'product-modal-placeholder';
                    placeholder.innerHTML = '<i class="fas fa-box"></i>';
                    media.appendChild(placeholder);
                }

                modal.classList.add('open');
                document.body.style.overflow = 'hidden';
            }

            function closeProductModal(event) {
                if (event && event.target !== event.currentTarget) return;

                const modal = document.getElementById('product-modal');
                if (!modal) return;

                modal.classList.remove('open');
                document.body.style.overflow = '';
                currentModalProductId = null;
            }

            function changeModalQty(step) {
                if (!currentModalProductId) return;

                const input = document.querySelector(`.child-qty[data-product-id="${currentModalProductId}"]`);
                if (!input) return;

                const min = parseInt(input.min || '0', 10);
                const max = parseInt(input.max || '99', 10);
                const current = parseInt(input.value || '0', 10) || 0;
                input.value = Math.max(min, Math.min(max, current + step));
                document.getElementById('product-modal-qty').textContent = input.value;
                updateCustomTotal();
            }

            function changeCustomQty(button, step) {
                const control = button.closest('.qty-control');
                const input = control ? control.querySelector('.child-qty') : null;
                if (!input) return;

                const min = parseInt(input.min || '0', 10);
                const max = parseInt(input.max || '1000', 10);
                const current = parseInt(input.value || '0', 10) || 0;
                input.value = Math.max(min, Math.min(max, current + step));
                if (String(input.dataset.productId) === String(currentModalProductId)) {
                    document.getElementById('product-modal-qty').textContent = input.value;
                }
                updateCustomTotal();
            }

            function updateCustomTotal() {
                let total = 0;
                const items = document.querySelectorAll('.child-qty');
                items.forEach(el => {
                    total += (parseInt(el.value) || 0) * (parseFloat(el.dataset.price) || 0);
                });

                // Theo yêu cầu: hoàn tiền = tổng tiền - 54.000đ (cho mua tự chọn)
                let hoan = total > 0 ? (total - 54000) : 0;
                if (hoan < 0) hoan = 0;

                renderStats(total, hoan);
            }

            function updateSingleTotal() {
                const qty = parseInt(document.getElementById('single-qty').value) || 0;
                let total = qty * basePrice;
                let hoan = qty * baseHoan;
                renderStats(total, hoan);
            }

            function renderStats(total, hoan) {
                const payableTotal = total + shippingFee;
                document.getElementById('display-total').textContent = formatVN(total);
                const hoanEl = document.getElementById('display-hoan');
                if (hoanEl) hoanEl.textContent = formatVN(hoan);
                document.getElementById('display-pay').textContent = formatVN(payableTotal);
                updateFormAction(payableTotal);

            }

            // Khởi tạo ban đầu
            if ({{ $isCustom ? 'true' : 'false' }}) {
                updateCustomTotal();
            } else {
                updateSingleTotal();
            }

            function updateFormAction(total) {
                const wallet = {{ auth()->user()->vi_tien_mua_hang ?? 0 }};
                const form = document.getElementById('order-form');

                if (form.dataset.isCustom === '1') {
                    form.action = "{{ route('admin.shop.confirm') }}";
                    return;
                }

                if (wallet >= total) {
                    form.action = "{{ route('admin.orders.store') }}";
                } else {
                    form.action = "{{ route('payos.create-payment-link') }}";
                }
            }

            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') closeProductModal();
            });
        </script>
    @endpush
@endsection
