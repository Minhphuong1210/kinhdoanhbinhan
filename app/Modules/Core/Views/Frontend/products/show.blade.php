@extends('Core::Frontend.layouts.app')

@section('title', $product->name)
@section('meta_description', $product->description ?: $product->name)
@if($product->image)@section('og_image', cdn($product->image))@endif

@php
    // Ảnh/video: video trước (nếu có), rồi ảnh chính, ảnh phụ
    $media = [];
    if ($product->video) {
        preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $product->video, $yt);
        $media[] = ['type' => 'video', 'youtube' => $yt[1] ?? null, 'url' => filter_var($product->video, FILTER_VALIDATE_URL) ? $product->video : cdn($product->video)];
    }
    if ($product->image) {
        $media[] = ['type' => 'image', 'url' => cdn($product->image)];
    }
    foreach ((array) ($product->image_extra ?? []) as $img) {
        $media[] = ['type' => 'image', 'url' => cdn($img)];
    }

    $hasDiscount = $product->old_price && $product->old_price > $product->price;
    $discount    = $hasDiscount ? round((1 - $product->price / $product->old_price) * 100) : 0;
    $buyUrl      = route('admin.shop.buy', $product) . ($product->active_children_count > 0 ? '?mode=custom' : '');
    $licenses    = array_values(array_filter((array) ($product->licenses ?? [])));
    $hotlineRaw  = preg_replace('/[^0-9+]/', '', $site['hotline'] ?? '');
@endphp

@push('styles')
<style>
    .pd-breadcrumb { background: #fff; border-bottom: 1px solid var(--line); }
    .pd-breadcrumb .breadcrumb { margin: 0; padding: 12px 0; font-size: 14px; }
    .gallery { background: #fff; border: 1px solid var(--line); border-radius: 18px; padding: 14px; position: sticky; top: 140px; }
    .gallery-stage { position: relative; aspect-ratio: 1 / 1; border-radius: 12px; overflow: hidden; background: var(--primary-soft); }
    .gallery-stage .slide { position: absolute; inset: 0; display: none; }
    .gallery-stage .slide.active { display: block; }
    .gallery-stage img, .gallery-stage video, .gallery-stage iframe { width: 100%; height: 100%; object-fit: contain; border: 0; background: #fff; }
    .gallery-nav { position: absolute; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; border-radius: 50%; border: 0; background: rgba(255,255,255,.92); box-shadow: var(--shadow); z-index: 2; }
    .gallery-nav.prev { left: 10px; } .gallery-nav.next { right: 10px; }
    .gallery-thumbs { display: flex; gap: 10px; margin-top: 12px; overflow-x: auto; padding-bottom: 4px; }
    .gallery-thumbs button { flex: 0 0 72px; height: 72px; border-radius: 10px; overflow: hidden; border: 2px solid transparent; padding: 0; background: var(--primary-soft); position: relative; }
    .gallery-thumbs button.active { border-color: var(--primary); }
    .gallery-thumbs img { width: 100%; height: 100%; object-fit: cover; }
    .gallery-thumbs .play { position: absolute; inset: 0; display: grid; place-items: center; color: #fff; background: rgba(0,0,0,.35); font-size: 20px; }

    .pd-info h1 { font-weight: 800; font-size: 1.7rem; line-height: 1.35; }
    .pd-meta { color: var(--muted); font-size: 14px; display: flex; gap: 16px; flex-wrap: wrap; }
    .pd-price-box { background: linear-gradient(120deg, #fff6f5, #fff); border: 1px solid #ffe1de; border-radius: 14px; padding: 18px 20px; margin: 18px 0; }
    .pd-price { color: var(--danger); font-size: 2rem; font-weight: 800; line-height: 1; }
    .pd-price-old { color: var(--muted); text-decoration: line-through; font-size: 1.05rem; }
    .pd-save { background: var(--danger); color: #fff; font-weight: 700; font-size: 13px; border-radius: 999px; padding: 4px 10px; }
    .pd-desc { color: #33504c; line-height: 1.75; }
    .pd-attr { list-style: none; padding: 0; margin: 18px 0; display: grid; gap: 10px; }
    .pd-attr li { display: flex; gap: 10px; }
    .pd-attr .k { color: var(--muted); min-width: 120px; }
    .pd-attr .tag { display: inline-block; background: var(--primary-soft); color: var(--primary-dark); border-radius: 999px; padding: 2px 10px; font-size: 13px; margin: 0 6px 6px 0; }
    .pd-actions .btn { padding: .85rem 1.4rem; font-size: 1rem; }
    .pd-commit { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 22px; }
    .pd-commit div { background: #fff; border: 1px solid var(--line); border-radius: 12px; padding: 12px; font-size: 13.5px; display: flex; gap: 10px; align-items: center; }
    .pd-commit i { color: var(--primary); font-size: 18px; }

    .pd-tabs { background: #fff; border: 1px solid var(--line); border-radius: 18px; overflow: hidden; }
    .pd-tabs .nav-tabs { border-bottom: 1px solid var(--line); padding: 0 12px; flex-wrap: nowrap; overflow-x: auto; }
    .pd-tabs .nav-link { border: 0; color: var(--muted); font-weight: 600; padding: 16px 18px; white-space: nowrap; border-bottom: 3px solid transparent; border-radius: 0; }
    .pd-tabs .nav-link.active { color: var(--primary-dark); border-bottom-color: var(--primary); background: none; }
    .pd-tabs .tab-pane { padding: 26px; }
    .license-file { display: flex; align-items: center; gap: 12px; border: 1px solid var(--line); border-radius: 12px; padding: 14px; color: var(--ink); height: 100%; }
    .license-file i { font-size: 30px; color: var(--danger); }

    .mobile-buybar { position: fixed; left: 0; right: 0; bottom: 0; background: #fff; border-top: 1px solid var(--line); padding: 10px 14px; z-index: 1035; display: none; box-shadow: 0 -6px 18px rgba(0,0,0,.06); }
    @media (max-width: 991.98px) {
        .gallery { position: static; }
        .pd-info h1 { font-size: 1.35rem; }
        .pd-price { font-size: 1.6rem; }
        .mobile-buybar { display: flex; gap: 10px; align-items: center; }
        body { padding-bottom: 70px; }
        .float-contact { bottom: 86px; }
    }
</style>
@endpush

@section('content')
<div class="pd-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('frontend.product.index') }}">Sản phẩm</a></li>
                @if($product->category)
                    <li class="breadcrumb-item"><a href="{{ route('frontend.product.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ \Illuminate\Support\Str::limit($product->name, 50) }}</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section pt-4">
    <div class="container">
        <div class="row g-4 g-lg-5">
            {{-- Thư viện ảnh / video --}}
            <div class="col-lg-6">
                <div class="gallery">
                    <div class="gallery-stage" id="galleryStage">
                        @forelse($media as $i => $item)
                            <div class="slide {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
                                @if($item['type'] === 'video' && $item['youtube'])
                                    <iframe src="https://www.youtube.com/embed/{{ $item['youtube'] }}" title="Video {{ $product->name }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
                                @elseif($item['type'] === 'video')
                                    <video src="{{ $item['url'] }}" controls playsinline preload="metadata"></video>
                                @else
                                    <img src="{{ $item['url'] }}" alt="{{ $product->name }} - ảnh {{ $i + 1 }}" {{ $i > 0 ? 'loading=lazy' : '' }}>
                                @endif
                            </div>
                        @empty
                            <div class="slide active d-grid" style="place-items:center"><i class="fas fa-leaf" style="font-size:64px;color:var(--primary);opacity:.35"></i></div>
                        @endforelse
                        @if(count($media) > 1)
                            <button type="button" class="gallery-nav prev" onclick="gallery.go(-1)" aria-label="Ảnh trước"><i class="fas fa-chevron-left"></i></button>
                            <button type="button" class="gallery-nav next" onclick="gallery.go(1)" aria-label="Ảnh sau"><i class="fas fa-chevron-right"></i></button>
                        @endif
                    </div>
                    @if(count($media) > 1)
                        <div class="gallery-thumbs">
                            @foreach($media as $i => $item)
                                <button type="button" class="{{ $i === 0 ? 'active' : '' }}" onclick="gallery.show({{ $i }})" aria-label="Xem {{ $item['type'] === 'video' ? 'video' : 'ảnh ' . ($i + 1) }}">
                                    @if($item['type'] === 'video')
                                        @if($item['youtube'])<img src="https://img.youtube.com/vi/{{ $item['youtube'] }}/mqdefault.jpg" alt="">@endif
                                        <span class="play"><i class="fas fa-play"></i></span>
                                    @else
                                        <img src="{{ $item['url'] }}" alt="" loading="lazy">
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Thông tin mua hàng --}}
            <div class="col-lg-6 pd-info">
                @if($product->category)
                    <a href="{{ route('frontend.product.index', ['category' => $product->category->slug]) }}" class="eyebrow">{{ $product->category->name }}</a>
                @endif
                <h1>{{ $product->name }}</h1>
                <div class="pd-meta">
                    <span><i class="far fa-eye me-1"></i>{{ number_format((int) $product->view) }} lượt xem</span>
                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>Còn hàng</span>
                    @if($product->khoi_luong)<span><i class="fas fa-weight-hanging me-1"></i>{{ $product->khoi_luong }}</span>@endif
                </div>

                <div class="pd-price-box">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <span class="pd-price">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                        @if($hasDiscount)
                            <span class="pd-price-old">{{ number_format($product->old_price, 0, ',', '.') }}₫</span>
                            <span class="pd-save">-{{ $discount }}%</span>
                        @endif
                    </div>
                    @if($hasDiscount)
                        <div class="small text-muted mt-2">Tiết kiệm {{ number_format($product->old_price - $product->price, 0, ',', '.') }}₫ so với giá niêm yết</div>
                    @endif
                </div>

                @if($product->description)
                    <p class="pd-desc">{{ $product->description }}</p>
                @endif

                <ul class="pd-attr">
                    @if($product->origin)
                        <li><span class="k">Xuất xứ</span><span>{{ \Illuminate\Support\Str::limit(strip_tags($product->origin), 120) }}</span></li>
                    @endif
                    @if($product->tags->isNotEmpty())
                        <li><span class="k">Thẻ</span><span>@foreach($product->tags as $tag)<span class="tag">{{ $tag->name }}</span>@endforeach</span></li>
                    @endif
                    @if($product->active_children_count > 0)
                        <li><span class="k">Hình thức</span><span>Mua tự chọn {{ $product->active_children_count }} sản phẩm trong combo</span></li>
                    @endif
                </ul>

                <div class="pd-actions d-flex flex-wrap gap-2">
                    <a href="{{ $buyUrl }}" class="btn btn-brand flex-grow-1"><i class="fas fa-cart-shopping me-2"></i>{{ $product->active_children_count > 0 ? 'Chọn sản phẩm & mua' : 'Mua ngay' }}</a>
                    @if($hotlineRaw)
                        <a href="tel:{{ $hotlineRaw }}" class="btn btn-outline-brand"><i class="fas fa-phone-alt me-2"></i>Tư vấn: {{ $site['hotline'] }}</a>
                    @endif
                </div>
                @guest
                    <p class="small text-muted mt-2 mb-0"><i class="fas fa-info-circle me-1"></i>Vui lòng <a href="{{ route('admin.login') }}">đăng nhập</a> hoặc <a href="{{ route('admin.register') }}">đăng ký</a> để đặt mua.</p>
                @endguest

                <div class="pd-commit">
                    <div><i class="fas fa-certificate"></i> Sản phẩm chính hãng</div>
                    <div><i class="fas fa-truck-fast"></i> Giao hàng toàn quốc</div>
                    <div><i class="fas fa-box-open"></i> Được kiểm hàng khi nhận</div>
                    <div><i class="fas fa-rotate-left"></i> Đổi trả theo chính sách</div>
                </div>
                <div class="small mt-3">
                    <a href="{{ route('frontend.policy.show', 'chinh-sach-van-chuyen-giao-nhan') }}">Vận chuyển</a> ·
                    <a href="{{ route('frontend.policy.show', 'chinh-sach-doi-tra-hoan-tien') }}">Đổi trả</a> ·
                    <a href="{{ route('frontend.policy.show', 'chinh-sach-thanh-toan') }}">Thanh toán</a>
                </div>
            </div>
        </div>

        {{-- Mô tả chi tiết --}}
        <div class="pd-tabs mt-5">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-desc" type="button" role="tab">Mô tả sản phẩm</button></li>
                @if($product->instructions)
                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-guide" type="button" role="tab">Hướng dẫn &amp; khuyến cáo</button></li>
                @endif
                @if($licenses)
                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-license" type="button" role="tab">Giấy phép, chứng nhận</button></li>
                @endif
                @if($product->origin)
                    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-origin" type="button" role="tab">Nguồn gốc</button></li>
                @endif
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active rich-content" id="tab-desc" role="tabpanel">
                    {!! clean_html($product->content ?: '<p class="text-muted">Thông tin sản phẩm đang được cập nhật.</p>') !!}
                </div>
                @if($product->instructions)
                    <div class="tab-pane fade rich-content" id="tab-guide" role="tabpanel">{!! clean_html($product->instructions) !!}</div>
                @endif
                @if($licenses)
                    <div class="tab-pane fade" id="tab-license" role="tabpanel">
                        <div class="row g-3">
                            @foreach($licenses as $file)
                                <div class="col-6 col-md-4 col-lg-3">
                                    @if(\Illuminate\Support\Str::endsWith(strtolower($file), '.pdf'))
                                        <a href="{{ cdn($file) }}" target="_blank" rel="noopener" class="license-file">
                                            <i class="fas fa-file-pdf"></i><span><strong class="d-block">Giấy chứng nhận {{ $loop->iteration }}</strong><small class="text-muted">Xem file PDF</small></span>
                                        </a>
                                    @else
                                        <a href="{{ cdn($file) }}" target="_blank" rel="noopener"><img src="{{ cdn($file) }}" class="img-fluid rounded border" alt="Giấy phép {{ $loop->iteration }}" loading="lazy"></a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if($product->origin)
                    <div class="tab-pane fade rich-content" id="tab-origin" role="tabpanel">{!! nl2br(e(strip_tags($product->origin))) !!}</div>
                @endif
            </div>
        </div>

        {{-- Sản phẩm liên quan --}}
        @if($related->isNotEmpty())
            <div class="mt-5">
                <div class="section-title"><div><h2>Sản phẩm <span>liên quan</span></h2></div></div>
                <div class="row g-3">
                    @foreach($related->take(4) as $item)
                        <div class="col-6 col-md-3">@include('Core::Frontend.home.partials.product-card', ['product' => $item])</div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

{{-- Thanh mua hàng cố định trên mobile --}}
<div class="mobile-buybar">
    <div class="flex-grow-1">
        <div class="fw-bold text-danger fs-5 lh-1">{{ number_format($product->price, 0, ',', '.') }}₫</div>
        @if($hasDiscount)<small class="text-muted text-decoration-line-through">{{ number_format($product->old_price, 0, ',', '.') }}₫</small>@endif
    </div>
    <a href="{{ $buyUrl }}" class="btn btn-brand"><i class="fas fa-cart-shopping me-1"></i>Mua ngay</a>
</div>
@endsection

@push('scripts')
<script>
    var gallery = (function () {
        var slides = document.querySelectorAll('#galleryStage .slide');
        var thumbs = document.querySelectorAll('.gallery-thumbs button');
        var current = 0;

        function show(i) {
            if (!slides.length) return;
            current = (i + slides.length) % slides.length;
            slides.forEach(function (s, idx) {
                var active = idx === current;
                s.classList.toggle('active', active);
                // Dừng video khi chuyển ảnh
                if (!active) {
                    var v = s.querySelector('video'); if (v) v.pause();
                    var f = s.querySelector('iframe'); if (f) f.src = f.src;
                }
            });
            thumbs.forEach(function (t, idx) { t.classList.toggle('active', idx === current); });
        }

        return { show: show, go: function (step) { show(current + step); } };
    })();
</script>
@endpush
