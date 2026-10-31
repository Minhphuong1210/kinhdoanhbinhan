@extends('Core::Admin.layouts.app')
@section('title', 'Đặt mua: ' . $product->name)
@section('page-title', 'Đặt hàng')
@push('styles')
    <style>
        .order-wrap{display:grid;grid-template-columns:340px 1fr;gap:24px;align-items:flex-start}
        .product-card{background:#fff;border-radius:14px;border:1.5px solid #e2e8f0;overflow:hidden;position:sticky;top:20px}
        .product-card .prod-img{width:100%;}
        .product-card .prod-img-placeholder{width:100%;height:200px;background:linear-gradient(135deg,#4e8ef7,#6c63ff);display:flex;align-items:center;justify-content:center;font-size:48px;color:rgba(255,255,255,.4)}
        .product-card .prod-body{padding:18px}
        .product-card .prod-cat{font-size:11px;color:#4e8ef7;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
        .product-card .prod-name{font-size:16px;font-weight:700;color:#1e2a3a;line-height:1.4;margin-bottom:12px}
        .price-now{font-size:26px;font-weight:800;color:#4e8ef7;margin-bottom:2px}
        .price-old{font-size:13px;color:#adb5bd;text-decoration:line-through;margin-bottom:8px}
        .hoan-badge{background:#dcfce7;color:#16a34a;padding:5px 12px;border-radius:6px;font-size:13px;font-weight:600;display:inline-block;margin-bottom:14px}
        .discount-badge{background:#fee2e2;color:#dc2626;padding:3px 9px;border-radius:5px;font-size:12px;font-weight:700;margin-left:8px}
        .desc-text{font-size:13px;color:#5a6a7e;line-height:1.6;border-top:1px solid #f1f5f9;padding-top:14px;margin-top:4px}

        .order-form{background:#fff;border-radius:14px;border:1.5px solid #e2e8f0;padding:24px}
        .section-head{font-size:12px;font-weight:700;color:red;text-transform:uppercase;letter-spacing:.7px;margin:20px 0 14px;display:flex;align-items:center;gap:8px}
        .section-head::after{content:'';flex:1;height:1px;background:#f1f5f9}
        .section-head:first-child{margin-top:0}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        .form-row.full{grid-template-columns:1fr}
        .form-group{display:flex;flex-direction:column;gap:5px}
        .form-group label{font-size:12px;font-weight:600;color:#374151}
        .input-wrap{position:relative}
        .input-wrap i{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:12px;pointer-events:none}
        .form-control{width:100%;padding:9px 14px 9px 33px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;font-family:inherit;color:#1e2a3a;outline:none;transition:.2s;background:#fff}
        .form-control:focus{border-color:#4e8ef7;box-shadow:0 0 0 3px rgba(78,142,247,.1)}
        .form-control.is-invalid{border-color:#ef4444}
        .err-msg{font-size:11px;color:#ef4444}

        .confirm-box{background:#fffbeb;border:1.5px solid #fde68a;border-radius:10px;padding:14px 16px;margin-top:20px;display:flex;gap:12px;align-items:flex-start}
        .confirm-box input[type=checkbox]{width:18px;height:18px;flex-shrink:0;margin-top:2px;accent-color:#4e8ef7;cursor:pointer}
        .confirm-box label{font-size:13px;color:#92400e;line-height:1.5;cursor:pointer}
        .confirm-box label b{color:#78350f}

        .btn-order{width:100%;padding:13px;background:linear-gradient(135deg,#4e8ef7,#6c63ff);color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;font-family:inherit;transition:.2s;margin-top:18px}
        .btn-order:hover{opacity:.9;transform:translateY(-1px);box-shadow:0 6px 20px rgba(78,142,247,.3)}
        .btn-order:disabled{opacity:.5;cursor:not-allowed;transform:none;box-shadow:none}

        .alert-err{background:#fef2f2;border:1px solid #fecaca;border-radius:9px;padding:10px 14px;font-size:13px;color:#dc2626;margin-bottom:16px}
        .child-table{width:100%;border-collapse:separate;border-spacing:0;margin-top:10px;border:1px solid #f1f5f9;border-radius:8px;overflow:hidden}
        .child-table th{background:#f8faff;padding:10px;font-size:11px;color:#8a9bb0;text-transform:uppercase;text-align:left;border-bottom:1px solid #f1f5f9}
        .child-table td{padding:12px 10px;font-size:13px;color:#1e2a3a;border-bottom:1px solid #f1f5f9}
        .child-table tr:last-child td{border-bottom:none}
        .qty-input{width:60px;padding:5px;border:1.5px solid #e2e8f0;border-radius:6px;text-align:center;font-size:13px;font-family:inherit}
        .summary-box{background:#f8faff;border:1.5px solid #4e8ef7;border-radius:10px;padding:16px;margin-top:20px}
        .summary-row{display:flex;justify-content:space-between;margin-bottom:8px;font-size:14px}
        .summary-row.total{border-top:1px solid #e2e8f0;padding-top:10px;margin-top:5px;font-weight:800;font-size:18px;color:#4e8ef7}
        .summary-row.hoan{color:#16a34a;font-weight:700}

        @media(max-width:768px){.order-wrap{grid-template-columns:1fr}.product-card{position:static}.form-row{grid-template-columns:1fr}}
    </style>
@endpush
@section('content')

    <div style="margin-bottom:16px">
        <a href="{{ route('admin.shop.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại gói sản phẩm</a>
    </div>

    <div style="    padding: 7px 10px 15px;
    background-color: #91e778;
    font-size: 20px;
    font-weight: bold;
    text-transform: uppercase;
    text-align: center;
    line-height:40px;

    ">
        <p>Nếu đặt hàng cho người khác xin điền đầy đủ thông tin người nhận và liên hệ để nhận hàng</p>

        <p> Bên vận chuyển giao hàng theo địa chỉ cũ ( 3 cấp ) trước sáp nhập. </p>


         <p>   Để đảm bảo công tác vận hành và giao hàng được chính xác – nhanh chóng – hiệu quả. </p>

        <p>    Vui lòng chọn đúng Tỉnh/ TP- Quận/ Huyện- Xã/ Phường </p>
    </div>



    @if($errors->any())
        <div class="alert-err"><ul style="list-style:none;padding:0">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="order-wrap">

        {{-- === THÔNG TIN SẢN PHẨM === --}}
        <div class="product-card">
            @if($product->image)
                <img class="prod-img" src="{{ cdn($product->image) }}" alt="{{ $product->name }}">
            @else
                <div class="prod-img-placeholder"><i class="fas fa-box"></i></div>
            @endif
            <div class="prod-body">
                @if($product->category)
                    <div class="prod-cat">{{ $product->category->name }}</div>
                @endif
                <div class="prod-name">{{ $product->name }}</div>

                @php
                    $discount = ($product->old_price && $product->old_price > $product->price)
                        ? round((1 - $product->price / $product->old_price) * 100) : 0;
                @endphp

                @if($product->old_price && $product->old_price > $product->price)
                    <div class="price-old">
                        {{ number_format($product->old_price, 0, ',', '.') }}₫
                        <span class="discount-badge">-{{ $discount }}%</span>
                    </div>
                @endif
                <div class="price-now">{{ number_format($product->price, 0, ',', '.') }}₫</div>

                @if($product->price_hoan_tien)
                    <div class="hoan-badge"><i class="fas fa-undo"></i> Hoàn {{ number_format($product->price_hoan_tien, 0, ',', '.') }}₫</div>
                @endif

                @if($product->description)
                    <div class="desc-text">{{ strip_tags($product->description) }}</div>
                @endif
            </div>
        </div>

        {{-- === FORM ĐẶT HÀNG === --}}
        <div class="order-form">
            <form method="POST" action="{{ route('admin.orders.store') }}" id="order-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                {{-- NGƯỜI NHẬN --}}
                <div class="section-head"><i class="fas fa-user" style="color:#4e8ef7"></i> Thông tin người nhận</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tên người nhận <span style="color:red">*</span></label>
                        <div class="input-wrap"><i class="fas fa-user"></i>
                            <input type="text" name="receive_user" class="form-control {{ $errors->has('receive_user') ? 'is-invalid' : '' }}" value="{{ old('receive_user') }}" placeholder="Điền đầy đủ họ và tên" required>
                        </div>
                        @error('receive_user')<div class="err-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại <span style="color:red">*</span></label>
                        <div class="input-wrap"><i class="fas fa-phone"></i>
                            <input type="tel" name="receive_phone" class="form-control {{ $errors->has('receive_phone') ? 'is-invalid' : '' }}" value="{{ old('receive_phone') }}" placeholder="0987654321" required>
                        </div>
                        @error('receive_phone')<div class="err-msg">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- ĐỊA CHỈ --}}
                <div class="section-head"><i class="fas fa-map-marker-alt" style="color:#4e8ef7"></i> Địa chỉ nhận hàng (chọn theo danh sách Pancake — tỉnh/TP, quận/huyện, phường/xã) </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tỉnh / Thành phố <span style="color:red">*</span></label>
                        <div class="input-wrap"><i class="fas fa-city"></i>
                            <select name="province_id" id="prov" class="form-control" onchange="loadDist(this.value)" required>
                                <option value="">— Chọn tỉnh/thành —</option>
                                @foreach($provinces as $p)
                                    <option value="{{ $p->id }}" >{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Quận / Huyện <span style="color:red">*</span></label>
                        <div class="input-wrap"><i class="fas fa-map"></i>
                            <select name="district_id" id="dist" class="form-control" onchange="loadWard(this.value)" required>
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
                        <label>Địa chỉ cụ thể <span style="color:red; text-transform: uppercase">(Không cần ghi xã, phường, quận, huyện,... chỉ cần ghi số nhà và thôn xóm) *</span></label>
                        <div class="input-wrap"><i class="fas fa-home"></i>
                            <input type="text" name="receive_address" class="form-control {{ $errors->has('receive_address') ? 'is-invalid' : '' }}" value="" placeholder="Ví dụ: Tầng 1, số nhà 3 , ngõ 38, phố Văn La" required>
                        </div>
                        @error('receive_address')<div class="err-msg">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- GHI CHÚ --}}
                <div class="section-head"><i class="fas fa-sticky-note" style="color:#4e8ef7"></i> Ghi chú đơn hàng</div>
                <div class="form-row full">
                    <div class="form-group">
                        <div class="input-wrap"><i class="fas fa-comment" style="top:14px;transform:none"></i>
                            <textarea name="note" class="form-control" rows="2" style="padding-left:33px;resize:vertical" placeholder="Yêu cầu đặc biệt, thời gian giao hàng...">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- SỐ LƯỢNG / CHỌN SẢN PHẨM --}}
                @if($isCustom)
                    <div class="section-head"><i class="fas fa-list-ul" style="color:#ff9f43"></i> Chọn sản phẩm mua tự chọn</div>
                    <table class="child-table">
                        <thead>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th style="text-align:right">Giá (₫)</th>
                            <th style="text-align:center">Số lượng</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($children as $child)
                            <tr>
                                <td>
                                    <b>{{ $child->name }}</b>
                                    <input type="hidden" name="child_ids[]" value="{{ $child->id }}">
                                </td>
                                <td style="text-align:right">{{ number_format($child->price, 0, ',', '.') }}</td>
                                <td style="text-align:center">
                                    <input type="number" name="child_qtys[]" class="qty-input child-qty"
                                           data-price="{{ $child->price }}" value="0" min="0" max="99"
                                           oninput="updateCustomTotal()">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div style="font-size:11px;color:#8a9bb0;margin-top:8px"><i class="fas fa-info-circle"></i> Vui lòng nhập số lượng vào ô tương ứng (vd: nhập 1 để mua). Sản phẩm số lượng 0 sẽ không được tính.</div>
                @else
                    <div class="section-head"><i class="fas fa-boxes" style="color:#4e8ef7"></i> Số lượng</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Số lượng đặt mua</label>
                            <div class="input-wrap"><i class="fas fa-hashtag"></i>
                                <input type="number" name="qty" id="single-qty" class="form-control" value="{{ old('qty', 1) }}" min="1" max="99" oninput="updateSingleTotal()" required>
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
                    <div class="summary-row hoan">
                        <span>Tiền hoàn dự kiến (đã bao gồm VAT):</span>
                        <span id="display-hoan">0₫</span>
                    </div>
                    <div class="summary-row total">
                        <span>Thanh toán:</span>
                        <span id="display-pay">0₫</span>
                    </div>
                    <input type="hidden" name="total_price_hidden" id="input-total" value="0">
                    <input type="hidden" name="total_hoan_hidden" id="input-hoan" value="0">
                </div>

                {{-- XÁC NHẬN --}}
                <div class="confirm-box">
                    <input type="checkbox" id="confirm-check" name="confirmed" value="1" onchange="toggleSubmit(this)" {{ old('confirmed') ? 'checked' : '' }}>
                    <label for="confirm-check">
                        Tôi xác nhận đã kiểm tra và <b>xác nhận thông tin trên là chính xác, không có sai sót</b>. Nếu thông tin sai dẫn đến không nhận được hàng hoặc phát sinh vấn đề, <b>tôi xin chịu hoàn toàn trách nhiệm</b>.
                    </label>
                </div>

                <button type="submit" id="btn-submit" class="btn-order" disabled>
                    <i class="fas fa-check-circle"></i> Xác nhận đặt mua
                </button>
            </form>
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
                const d = document.getElementById('dist'), w = document.getElementById('ward');
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
            const isCustom = {{ (isset($children) && $children->count() > 0) ? 'true' : 'false' }};
            const basePrice = {{ $product->price }};
            const baseHoan = {{ $product->price_hoan_tien ?? 0 }};

            function formatVN(n) { return new Intl.NumberFormat('vi-VN').format(n) + '₫'; }

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
                document.getElementById('display-total').textContent = formatVN(total);
                document.getElementById('display-hoan').textContent = formatVN(hoan);
                document.getElementById('display-pay').textContent = formatVN(total);
                document.getElementById('input-total').value = total;
                document.getElementById('input-hoan').value = hoan;
                updateFormAction(total);

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

                if (wallet >= total) {
                    form.action = "{{ route('admin.orders.store') }}";
                } else {
                    form.action = "{{ route('payos.create-payment-link') }}";
                }
            }

        </script>
    @endpush
@endsection
