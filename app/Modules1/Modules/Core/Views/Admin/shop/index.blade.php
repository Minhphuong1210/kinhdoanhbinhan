@extends('Core::Admin.layouts.app')
@section('title', 'Gói sản phẩm')
@section('page-title', 'Gói sản phẩm')
@push('styles')
<style>
.shop-tabs{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:24px}
.shop-tab{padding:8px 20px;border-radius:30px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;border:2px solid #e2e8f0;color:#5a6a7e;transition:.2s;background:#fff}
.shop-tab:hover{border-color:#4e8ef7;color:#4e8ef7;text-decoration:none}
.shop-tab.active{background:linear-gradient(135deg,#4e8ef7,#6c63ff);color:#fff;border-color:transparent;box-shadow:0 4px 12px rgba(78,142,247,.35)}
.product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:20px}
.product-card{border-radius:14px;overflow:hidden;background:#fff;border:1.5px solid #e2e8f0;transition:.25s;position:relative}
.product-card:hover{transform:translateY(-4px);box-shadow:0 10px 30px rgba(78,142,247,.15);border-color:#4e8ef7}
.product-card .badge-discount{position:absolute;top:10px;left:10px;background:linear-gradient(135deg,#ff4757,#ff6b81);color:#fff;font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;z-index:2}
.product-card .badge-hoan{position:absolute;top:10px;right:10px;background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;z-index:2}
.product-card .img-wrap{width:100%;overflow:hidden;position:relative}
.product-card .img-wrap img{width:100%;height:100%;transition:.3s}
.product-card:hover .img-wrap img{transform:scale(1.05)}
.product-card .img-placeholder{width:100%;height:180px;background:linear-gradient(135deg,#4e8ef7,#6c63ff);display:flex;align-items:center;justify-content:center}
.product-card .body{padding:14px}
.product-card .cat-label{font-size:11px;color:#4e8ef7;font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px}
.product-card .prod-name{font-size:14px;font-weight:700;color:#1e2a3a;line-height:1.4;margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.product-card .desc{font-size:12px;color:#8a9bb0;line-height:1.5;margin-top:8px;margin-bottom:10px}
.product-card .price-row{display:flex;align-items:flex-end;gap:8px;margin-bottom:10px}
.product-card .price-now{font-size:20px;font-weight:800;color:#4e8ef7}
.product-card .price-old{font-size:12px;color:#adb5bd;text-decoration:line-through}
.product-card .hoan-badge{font-size:11px;background:#dcfce7;color:#16a34a;padding:3px 8px;border-radius:5px;font-weight:600}
.product-card .btn-buy{display:block;text-align:center;padding:9px;background:linear-gradient(135deg,#4e8ef7,#6c63ff);color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:.2s}
.product-card .btn-buy:hover{opacity:.88;color:#fff;text-decoration:none;transform:scale(.98)}
</style>
@endpush
@section('content')

{{-- ====== CATEGORY TABS ====== --}}
<div class="shop-tabs">
    <a href="{{ route('admin.shop.index') }}" class="shop-tab {{ !$activeCatId ? 'active' : '' }}">
        <i class="fas fa-th"></i> Tất cả
    </a>
    @foreach($categories as $cat)
    <a href="{{ route('admin.shop.index') }}?cat={{ $cat->id }}" class="shop-tab {{ $activeCatId == $cat->id ? 'active' : '' }}">
        {{ $cat->name }}
        <span style="opacity:.7;font-size:11px">({{ $cat->products_count }})</span>
    </a>
    @endforeach
</div>

{{-- Tiêu đề danh mục đang chọn --}}
@if($activeCategory)
<div style="margin-bottom:20px">
    <h5 style="color:#1e2a3a;font-weight:700"><i class="fas fa-folder-open" style="color:#4e8ef7"></i> {{ $activeCategory->name }}</h5>
</div>
@endif

{{-- ====== PRODUCT GRID ====== --}}
@if($products->count())
<div class="product-grid">
    @foreach($products as $product)
    @php
        $discount = ($product->old_price && $product->old_price > $product->price)
            ? round((1 - $product->price/$product->old_price) * 100)
            : 0;
    @endphp
    <div class="product-card">
        {{-- Badge % giảm --}}
        @if($discount > 0)
        <span class="badge-discount">-{{ $discount }}%</span>
        @endif
        {{-- Badge hoàn tiền --}}
        @if($product->price_hoan_tien)
{{--        <span class="badge-hoan">Hoàn {{ number_format($product->price_hoan_tien/1000,0)}}K</span>--}}
        @endif

        {{-- Ảnh --}}
        <div class="img-wrap">
            @if($product->image)
            <img src="{{ cdn($product->image) }}" alt="{{ $product->name }}">
            @else
            <div class="img-placeholder"><i class="fas fa-box" style="font-size:44px;color:rgba(255,255,255,.5)"></i></div>
            @endif
        </div>

        <div class="body">
            @if($product->category)
            <div class="cat-label">{{ $product->category->name }}</div>
            @endif
            <div class="prod-name">{{ $product->name }}</div>

            @if($product->description)
            <div class="desc">{{ strip_tags($product->description) }}</div>
            @endif

            {{-- Giá --}}
            <div class="price-row">
                <span class="price-now">{{ number_format($product->price,0,',','.') }}₫  @if($product->category->id==3)  <span >(bao gồm VAT và Ship) </span> @endif  </span>
                @if($product->old_price && $product->old_price > $product->price)
                <span class="price-old">{{ number_format($product->old_price,0,',','.') }}₫</span>
                @endif
            </div>
            @if($product->price_hoan_tien)
            <div style="margin-bottom:10px">
                <span class="hoan-badge"><i class="fas fa-undo"></i> Mức hoàn phí {{ number_format($product->price_hoan_tien,0,',','.') }}₫</span>
            </div>
            @endif

            <div style="display:flex;gap:8px">
                <a href="{{ route('admin.shop.buy', $product->id) }}" class="btn-buy" style="flex:1">
                    <i class="fas fa-shopping-cart"></i> Mua ngay
                </a>
                @if($product->children_count > 0)
                    <a href="{{ route('admin.shop.buy', $product->id) }}?mode=custom" class="btn-buy" style="flex:1;background:linear-gradient(135deg,#ff9f43,#ff6b6b)">
                        <i class="fas fa-list-ul"></i> Mua tự chọn
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
<div style="margin-top:24px">{{ $products->links() }}</div>

@else
<div class="card">
    <div class="card-body" style="text-align:center;padding:60px;color:#8a9bb0">
        <i class="fas fa-box-open" style="font-size:48px;margin-bottom:16px;display:block;opacity:.35"></i>
        <p>Chưa có sản phẩm nào{{ $activeCategory ? ' trong danh mục "'.$activeCategory->name.'"' : '' }}.</p>
    </div>
</div>
@endif

@endsection
