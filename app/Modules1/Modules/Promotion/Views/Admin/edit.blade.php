@extends('Core::Admin.layouts.app')
@section('title', 'Sửa: ' . $promotion->name)
@section('page-title', 'Sự kiện Khuyến mãi')

@push('styles')
<style>
    .edit-grid{display:grid;grid-template-columns:340px 1fr;gap:20px;align-items:flex-start}
    @media(max-width:900px){.edit-grid{grid-template-columns:1fr}}

    /* Card thông tin sự kiện */
    .info-card{background:#fff;border-radius:12px;border:1.5px solid #e2e8f0;padding:22px;position:sticky;top:20px}
    .info-card h3{font-size:15px;font-weight:700;color:#1e2a3a;margin:0 0 16px;display:flex;align-items:center;gap:8px}
    .fg{margin-bottom:14px}
    .fg label{display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px}
    .fg input,.fg select,.fg textarea{width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;color:#1e2a3a;outline:none;transition:.2s}
    .fg input:focus,.fg select:focus,.fg textarea:focus{border-color:#4e8ef7;box-shadow:0 0 0 3px rgba(78,142,247,.1)}
    .fg .row2{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .chk{display:flex;align-items:center;gap:8px;margin-bottom:14px}
    .chk input{width:16px;height:16px;accent-color:#4e8ef7}
    .chk label{font-size:13px;color:#374151}
    .btn-save{padding:9px 20px;background:linear-gradient(135deg,#4e8ef7,#6c63ff);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:.2s;width:100%}
    .btn-save:hover{opacity:.9}

    /* Panel sản phẩm + quà tặng */
    .products-panel{background:#fff;border-radius:12px;border:1.5px solid #e2e8f0;padding:22px}
    .products-panel h3{font-size:15px;font-weight:700;color:#1e2a3a;margin:0 0 16px;display:flex;align-items:center;gap:8px}

    /* Form thêm SP */
    .add-product-form{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap}
    .add-product-form select{flex:1;min-width:200px;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px}
    .btn-add-sm{padding:8px 16px;background:#10b981;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;transition:.2s;white-space:nowrap}
    .btn-add-sm:hover{background:#059669}

    /* Card sản phẩm */
    .pp-card{border:1.5px solid #e2e8f0;border-radius:10px;margin-bottom:16px;overflow:hidden}
    .pp-header{background:#f8faff;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #f1f5f9}
    .pp-header .pp-name{font-size:14px;font-weight:700;color:#1e2a3a}
    .pp-header .pp-price{font-size:13px;color:#4e8ef7;font-weight:600}
    .btn-remove{padding:4px 10px;background:#fef2f2;color:#ef4444;border:1px solid #fecaca;border-radius:6px;font-size:11px;cursor:pointer;transition:.2s}
    .btn-remove:hover{background:#ef4444;color:#fff}

    .pp-body{padding:14px 16px}

    /* Danh sách gift */
    .gift-list{margin-bottom:12px}
    .gift-item{display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:#fafbff;border-radius:8px;margin-bottom:6px;border:1px solid #f1f5f9}
    .gift-item .gift-info{font-size:13px;color:#374151}
    .gift-item .gift-type{font-size:10px;padding:2px 8px;border-radius:12px;font-weight:600;text-transform:uppercase}
    .type-product{background:#dcfce7;color:#16a34a}
    .type-discount_percent{background:#fef3c7;color:#d97706}
    .type-discount_amount{background:#fce7f3;color:#db2777}

    /* Form thêm gift */
    .add-gift-form{border-top:1px solid #f1f5f9;padding-top:12px}
    .add-gift-form h4{font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin:0 0 10px}
    .gift-type-tabs{display:flex;gap:6px;margin-bottom:12px;flex-wrap:wrap}
    .gift-type-tab{padding:6px 14px;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;border:1.5px solid #e2e8f0;background:#fff;color:#64748b;transition:.2s}
    .gift-type-tab.active{border-color:#4e8ef7;background:#eff6ff;color:#4e8ef7}
    .gift-type-tab:hover{border-color:#4e8ef7}
    .gift-fields{display:none}
    .gift-fields.active{display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end}
    .gift-fields .fg{margin-bottom:0;flex:1;min-width:150px}
    .gift-fields .btn-add-gift{padding:8px 16px;background:#6c63ff;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap}
    .gift-fields .btn-add-gift:hover{opacity:.9}

    .btn-back{display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#64748b;text-decoration:none;margin-bottom:16px}
    .btn-back:hover{color:#4e8ef7}
    .empty-gifts{text-align:center;padding:16px;color:#94a3b8;font-size:13px;font-style:italic}
    
    /* Select2 customizations to match theme */
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 8px !important;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #1e2a3a !important;
        font-size: 13px;
        line-height: normal !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 13px;
    }
    .select2-results__option {
        font-size: 13px;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <a href="{{ route('admin.promotions.index') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Quay lại danh sách
    </a>

    <div class="edit-grid">

        {{-- === CỘT TRÁI: THÔNG TIN SỰ KIỆN === --}}
        <div class="info-card">
            <h3><i class="fas fa-info-circle" style="color:#4e8ef7"></i> Thông tin sự kiện</h3>
            <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST">
                @csrf @method('PUT')

                <div class="fg">
                    <label>Tên sự kiện</label>
                    <input type="text" name="name" value="{{ old('name', $promotion->name) }}" required>
                </div>
                <div class="fg">
                    <div class="row2">
                        <div>
                            <label>Tháng</label>
                            <select name="month">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $promotion->month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label>Năm</label>
                            <input type="number" name="year" value="{{ $promotion->year }}" min="2020" max="2099">
                        </div>
                    </div>
                </div>
                <div class="fg">
                    <label>Mô tả</label>
                    <textarea name="description" rows="2">{{ $promotion->description }}</textarea>
                </div>
                <div class="chk">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ $promotion->is_active ? 'checked' : '' }}>
                    <label for="is_active">Kích hoạt</label>
                </div>
                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Lưu thay đổi</button>
            </form>
        </div>

        {{-- === CỘT PHẢI: SẢN PHẨM + QUÀ TẶNG === --}}
        <div class="products-panel">
            <h3><i class="fas fa-boxes" style="color:#10b981"></i> Sản phẩm & Khuyến mãi</h3>

            {{-- Form thêm sản phẩm --}}
            <form action="{{ route('admin.promotions.add-product', $promotion->id) }}" method="POST" class="add-product-form">
                @csrf
                <select name="product_id" class="searchable-select" required style="width: 100%;">
                    <option value="">— Chọn sản phẩm để thêm —</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ number_format($p->price, 0, ',', '.') }}₫)</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-add-sm" style="margin-top: 10px;"><i class="fas fa-plus"></i> Thêm SP</button>
            </form>

            {{-- Danh sách sản phẩm đã thêm --}}
            @forelse($promotion->productPromotions as $pp)
                <div class="pp-card">
                    <div class="pp-header">
                        <div>
                            <span class="pp-name">{{ $pp->product->name ?? '(Đã xoá)' }}</span>
                            <span class="pp-price">{{ number_format($pp->product->price ?? 0, 0, ',', '.') }}₫</span>
                        </div>
                        <form action="{{ route('admin.promotions.remove-product', [$promotion->id, $pp->id]) }}" method="POST" onsubmit="return confirm('Xoá SP này? (Cả quà tặng sẽ bị xoá)')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-remove"><i class="fas fa-times"></i> Xoá</button>
                        </form>
                    </div>
                    <div class="pp-body">
                        {{-- Danh sách quà tặng / khuyến mãi --}}
                        <div class="gift-list">
                            @forelse($pp->gifts as $gift)
                                <div class="gift-item">
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <span class="gift-type type-{{ $gift->type }}">
                                            @if($gift->type === 'product') 🎁 Tặng SP
                                            @elseif($gift->type === 'discount_percent') 💰 Giảm %
                                            @else 💵 Giảm tiền
                                            @endif
                                        </span>
                                        <span class="gift-info">{{ $gift->description }}</span>
                                    </div>
                                    <form action="{{ route('admin.promotions.remove-gift', $gift->id) }}" method="POST" onsubmit="return confirm('Xoá?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-remove" style="padding:2px 8px"><i class="fas fa-trash" style="font-size:10px"></i></button>
                                    </form>
                                </div>
                            @empty
                                <div class="empty-gifts"><i class="fas fa-info-circle"></i> Chưa có khuyến mãi nào cho SP này.</div>
                            @endforelse
                        </div>

                        {{-- Form thêm khuyến mãi --}}
                        <div class="add-gift-form">
                            <h4><i class="fas fa-plus-circle"></i> Thêm khuyến mãi</h4>
                            <div class="gift-type-tabs" data-pp="{{ $pp->id }}">
                                <div class="gift-type-tab active" data-type="product" onclick="switchGiftType(this, 'product', {{ $pp->id }})">🎁 Tặng sản phẩm</div>
                                <div class="gift-type-tab" data-type="discount_percent" onclick="switchGiftType(this, 'discount_percent', {{ $pp->id }})">💰 Giảm giá %</div>
                                <div class="gift-type-tab" data-type="discount_amount" onclick="switchGiftType(this, 'discount_amount', {{ $pp->id }})">💵 Trừ tiền</div>
                            </div>

                            {{-- Form tặng sản phẩm --}}
                            <form action="{{ route('admin.promotions.add-gift', $pp->id) }}" method="POST">
                                @csrf
                                <div class="gift-fields active" id="gift-product-{{ $pp->id }}">
                                    <input type="hidden" name="type" value="product">
                                    <div class="fg" style="flex: 1;">
                                        <label>Sản phẩm tặng</label>
                                        <select name="gift_product_id" class="searchable-select" required style="width: 100%;">
                                            <option value="">— Chọn SP —</option>
                                            @foreach($products as $gp)
                                                <option value="{{ $gp->id }}">{{ $gp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="fg" style="width:80px; flex: none;">
                                        <label>SL</label>
                                        <input type="number" name="quantity" value="1" min="1" max="99">
                                    </div>
                                    <button type="submit" class="btn-add-gift" style="margin-bottom: 2px;"><i class="fas fa-plus"></i> Thêm</button>
                                </div>
                            </form>

                            {{-- Form giảm giá % --}}
                            <form action="{{ route('admin.promotions.add-gift', $pp->id) }}" method="POST">
                                @csrf
                                <div class="gift-fields" id="gift-discount_percent-{{ $pp->id }}">
                                    <input type="hidden" name="type" value="discount_percent">
                                    <div class="fg">
                                        <label>Phần trăm giảm (%)</label>
                                        <input type="number" name="discount_value" step="0.01" min="0.01" max="100" placeholder="VD: 10">
                                    </div>
                                    <button type="submit" class="btn-add-gift"><i class="fas fa-plus"></i> Thêm</button>
                                </div>
                            </form>

                            {{-- Form trừ tiền --}}
                            <form action="{{ route('admin.promotions.add-gift', $pp->id) }}" method="POST">
                                @csrf
                                <div class="gift-fields" id="gift-discount_amount-{{ $pp->id }}">
                                    <input type="hidden" name="type" value="discount_amount">
                                    <div class="fg">
                                        <label>Số tiền giảm (₫)</label>
                                        <input type="number" name="discount_value" min="1" placeholder="VD: 50000">
                                    </div>
                                    <button type="submit" class="btn-add-gift"><i class="fas fa-plus"></i> Thêm</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-gifts" style="padding:40px">
                    <i class="fas fa-box-open" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4"></i>
                    Chưa có sản phẩm nào trong sự kiện. Hãy thêm sản phẩm ở trên.
                </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.searchable-select').select2({
                placeholder: "— Tìm kiếm và chọn sản phẩm —",
                allowClear: true,
                language: {
                    noResults: function() {
                        return "Không tìm thấy sản phẩm nào";
                    }
                }
            });
        });

        function switchGiftType(el, type, ppId) {
            // Toggle tab active
            const tabs = el.parentElement.querySelectorAll('.gift-type-tab');
            tabs.forEach(t => t.classList.remove('active'));
            el.classList.add('active');

            // Toggle form fields
            ['product', 'discount_percent', 'discount_amount'].forEach(t => {
                const fields = document.getElementById('gift-' + t + '-' + ppId);
                if (fields) {
                    fields.classList.toggle('active', t === type);
                }
            });
        }
    </script>
    @endpush
@endsection
