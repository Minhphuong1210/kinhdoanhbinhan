{{-- Thẻ sản phẩm dùng chung. Biến: $product --}}
@php
    $hasDiscount = $product->old_price && $product->old_price > $product->price;
    $discount    = $hasDiscount ? round((1 - $product->price / $product->old_price) * 100) : 0;
@endphp
<div class="product-card h-100">
    <a href="{{ route('frontend.product.show', $product->slug) }}" class="product-thumb">
        @if($product->image)
            <img src="{{ cdn($product->image) }}" alt="{{ $product->name }}" loading="lazy">
        @else
            <span class="product-thumb-empty"><i class="fas fa-leaf"></i></span>
        @endif
        @if($discount > 0)
            <span class="product-badge">-{{ $discount }}%</span>
        @endif
    </a>
    <div class="product-body">
        @if($product->relationLoaded('category') && $product->category)
            <div class="product-cat">{{ $product->category->name }}</div>
        @endif
        <a href="{{ route('frontend.product.show', $product->slug) }}" class="product-name">{{ $product->name }}</a>
        <div class="product-price">
            <span class="price-new">{{ number_format($product->price, 0, ',', '.') }}₫</span>
            @if($hasDiscount)
                <span class="price-old">{{ number_format($product->old_price, 0, ',', '.') }}₫</span>
            @endif
        </div>
        <a href="{{ route('frontend.product.show', $product->slug) }}" class="btn btn-outline-brand btn-sm w-100 mt-2">Xem chi tiết</a>
    </div>
</div>

@once
    @push('styles')
        <style>
            .product-card { background: #fff; border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden; display: flex; flex-direction: column; transition: transform .2s, box-shadow .2s; }
            .product-card:hover { transform: translateY(-4px); box-shadow: var(--shadow); }
            .product-thumb { position: relative; display: block; aspect-ratio: 1 / 1; background: var(--primary-soft); overflow: hidden; }
            .product-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform .35s; }
            .product-card:hover .product-thumb img { transform: scale(1.05); }
            .product-thumb-empty { position: absolute; inset: 0; display: grid; place-items: center; font-size: 42px; color: var(--primary); opacity: .4; }
            .product-badge { position: absolute; top: 10px; left: 10px; background: var(--danger); color: #fff; font-weight: 700; font-size: 12px; padding: 3px 9px; border-radius: 999px; }
            .product-body { padding: 14px; display: flex; flex-direction: column; flex: 1; }
            .product-cat { font-size: 11.5px; text-transform: uppercase; letter-spacing: .04em; color: var(--primary); font-weight: 700; margin-bottom: 4px; }
            .product-name { color: var(--ink); font-weight: 600; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.8em; }
            .product-name:hover { color: var(--primary); }
            .product-price { margin-top: auto; padding-top: 8px; display: flex; align-items: baseline; gap: 8px; flex-wrap: wrap; }
            .price-new { color: var(--danger); font-weight: 800; font-size: 16px; }
            .price-old { color: var(--muted); text-decoration: line-through; font-size: 13px; }
        </style>
    @endpush
@endonce
