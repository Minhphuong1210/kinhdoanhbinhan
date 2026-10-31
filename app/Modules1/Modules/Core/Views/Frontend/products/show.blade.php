@extends('Core::Frontend.layouts.app')

@section('title', $product->meta_title ?: $product->name)
@section('meta_description', $product->meta_description ?: $product->description)

@section('extra-styles')
    <style>
        .page-hero {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: #fff; padding: 36px 0 30px; text-align: center;
        }
        .page-hero h1 { font-size: 1.5rem; font-weight: 800; }
        .page-hero .breadcrumb { justify-content: center; background: none; padding: 0; margin: 8px 0 0; }
        .page-hero .breadcrumb-item,
        .page-hero .breadcrumb-item a { color: rgba(255,255,255,.75); font-size: .875rem; text-decoration: none; }
        .page-hero .breadcrumb-item.active { color: #fff; }
        .page-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.5); }

        /* ====== GALLERY ====== */
        .gallery-wrap {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,.14);
            background: #000;
            position: relative;
        }
        .gallery-main {
            width: 100%;
            /*aspect-ratio: 16/10;*/
            position: relative;
            background: #111;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .gallery-main img {
            width: 100%; height: 100%;
            object-fit: unset;
            display: none;
            transition: opacity .3s;
        }
        .gallery-main img.active { display: block; }
        .gallery-main .video-frame {
            position: absolute; inset: 0;
            display: none;
        }
        .gallery-main .video-frame.active { display: block; }
        .gallery-main .video-frame iframe,
        .gallery-main .video-frame video {
            width: 100%; height: 100%; border: 0; display: block;
        }

        /* Nav arrows */
        .gallery-nav {
            position: absolute;
            top: 50%; transform: translateY(-50%);
            z-index: 20;
            background: rgba(0,0,0,.45);
            color: #fff;
            border: none;
            width: 42px; height: 42px;
            border-radius: 50%;
            font-size: 1.1rem;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: background .2s;
        }
        .gallery-nav:hover { background: rgba(0,0,0,.75); }
        .gallery-nav.prev { left: 12px; }
        .gallery-nav.next { right: 12px; }

        /* Thumbnails */
        .gallery-thumbs {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .gallery-thumbs .thumb {
            width: 72px; height: 72px;
            border-radius: 8px;
            border: 2.5px solid transparent;
            cursor: pointer;
            overflow: hidden;
            position: relative;
            background: #111;
            flex-shrink: 0;
            transition: border-color .2s;
        }
        .gallery-thumbs .thumb.active,
        .gallery-thumbs .thumb:hover { border-color: var(--primary); }
        .gallery-thumbs .thumb img {
            width: 100%; height: 100%; object-fit: unset;
        }
        .gallery-thumbs .thumb .video-icon {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
            background: rgba(0,0,0,.55);
            color: #fff;
            font-size: 1.6rem;
        }

        /* ====== INFO PANEL ====== */
        .product-detail-title { font-size: 1.55rem; font-weight: 800; color: var(--text-dark); line-height: 1.3; }
        .product-price-new { font-size: 1.8rem; font-weight: 800; color: #e74c3c; }
        .product-price-old { font-size: 1rem; color: #aaa; text-decoration: line-through; }
        .badge-save { background: #e74c3c; color: #fff; padding: 4px 14px; border-radius: 50px; font-size: .78rem; font-weight: 700; }

        .info-row {
            display: flex; gap: 8px; align-items: center;
            padding: 10px 0; border-bottom: 1px solid var(--border-light); font-size: .9rem;
        }
        .info-row .label { color: var(--text-muted); min-width: 120px; }
        .info-row .value { font-weight: 500; color: var(--text-dark); }

        /* ====== RELATED ====== */
        .related-card {
            border: none; border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
            overflow: hidden; transition: transform .22s, box-shadow .22s;
            height: 100%;
        }
        .related-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(0,0,0,.13); }
        .related-card img { width: 100%;  object-fit: unset; }

        /* ====== DETAIL CONTENT ====== */
        .detail-content img { max-width: 100%; border-radius: 8px; }
        .detail-content p { line-height: 1.9; }

        /* ====== TABS ====== */
        .product-tabs .nav-tabs { border-bottom: 2px solid #eee; gap: 2px; }
        .product-tabs .nav-link {
            border: none; border-bottom: 3px solid transparent;
            color: #666; font-weight: 600; padding: 12px 24px;
            transition: all .2s;
        }
        .product-tabs .nav-link:hover { color: var(--primary); background: #f8f9fa; }
        .product-tabs .nav-link.active {
            color: var(--primary); border-bottom-color: var(--primary);
            background: transparent;
        }
        .tab-content { padding: 30px 0; }
    </style>
@endsection

@section('content')

    @php
        /* Build media array: video first (if exists), then images */
        $mediaList = [];

        if ($product->video) {
            preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $product->video, $ytm);
            $mediaList[] = [
                'type'    => 'video',
                'youtube' => $ytm[1] ?? null,
                'url'     => $product->video,
            ];
        }

        if ($product->image) {
            $mediaList[] = ['type' => 'image', 'url' =>  cdn($product->image)];
        }

        foreach ((array)($product->image_extra ?? []) as $img) {
            $mediaList[] = ['type' => 'image', 'url' => cdn($img)];
        }
    @endphp

    {{-- PAGE HERO --}}
    <div class="page-hero">
        <div class="container">
            <h1>{{ $product->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('frontend.product.index') }}">Sản phẩm</a></li>
                    <li class="breadcrumb-item active">{{ \Str::limit($product->name, 40) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section style="padding:48px 0">
        <div class="container">
            <div class="row g-5">

                {{-- ===== GALLERY ===== --}}
                <div class="col-lg-5">
                    @if(count($mediaList) > 0)
                        <div class="gallery-wrap">
                            <div class="gallery-main" id="galleryMain">

                                @if(count($mediaList) > 1)
                                    <button class="gallery-nav prev" onclick="galleryMove(-1)" aria-label="Trước">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <button class="gallery-nav next" onclick="galleryMove(1)" aria-label="Tiếp">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                @endif

                                @foreach($mediaList as $i => $media)
                                    @if($media['type'] === 'video')
                                        <div class="video-frame {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
                                            @if($media['youtube'])
                                                <iframe src="https://www.youtube.com/embed/{{ $media['youtube'] }}?rel=0"
                                                        allow="autoplay; encrypted-media" allowfullscreen
                                                        title="Video sản phẩm {{ $product->name }}"></iframe>
                                            @else
                                                <video src="{{ $media['url'] }}" controls></video>
                                            @endif
                                        </div>
                                    @else
                                        <img src="{{ $media['url'] }}"
                                             alt="{{ $product->name }} - ảnh {{ $i }}"
                                             class="{{ $i === 0 ? 'active' : '' }}"
                                             data-index="{{ $i }}">
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- THUMBNAILS --}}
                        @if(count($mediaList) > 1)
                            <div class="gallery-thumbs" id="galleryThumbs">
                                @foreach($mediaList as $i => $media)
                                    @if($media['type'] === 'video')
                                        <div class="thumb {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}" onclick="galleryGoto({{ $i }})" title="Video sản phẩm">
                                            @if($media['youtube'])
                                                <img src="https://img.youtube.com/vi/{{ $media['youtube'] }}/mqdefault.jpg" alt="Video thumbnail">
                                            @else
                                                <div style="width:100%;height:100%;background:#1a1a2e"></div>
                                            @endif
                                            <span class="video-icon"><i class="bi bi-play-circle-fill"></i></span>
                                        </div>
                                    @else
                                        <div class="thumb {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}" onclick="galleryGoto({{ $i }})" title="Ảnh {{ $i }}">
                                            <img src="{{ $media['url'] }}" alt="thumb {{ $i }}">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                    @else
                        {{-- No media --}}
                        <div class="gallery-wrap">
                            <div class="gallery-main" style="min-height:320px;background:var(--primary-light)">
                                <i class="bi bi-box-seam" style="font-size:5rem;color:var(--primary);opacity:.3"></i>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ===== THÔNG TIN ===== --}}
                <div class="col-lg-7">
                    <div class="product-detail-title mb-3">{{ $product->name }}</div>

                    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                        <span class="product-price-new">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                        @if($product->old_price && $product->old_price > $product->price)
                            <span class="product-price-old">{{ number_format($product->old_price, 0, ',', '.') }}₫</span>
                            <span class="badge-save">Tiết kiệm {{ number_format($product->old_price - $product->price, 0, ',', '.') }}₫</span>
                        @endif
                    </div>

                    @if($product->description)
                        <p style="font-size:.95rem;color:#555;line-height:1.8;margin-bottom:24px">
                            {{ $product->description }}
                        </p>
                    @endif

                    <div style="margin-bottom:28px">
                        @if($product->category)
                            <div class="info-row">
                                <span class="label"><i class="bi bi-tag me-2"></i>Danh mục</span>
                                <span class="value">{{ $product->category->name }}</span>
                            </div>
                        @endif
                        @if($product->tags->isNotEmpty())
                            <div class="info-row">
                                <span class="label"><i class="bi bi-hash me-2"></i>Tags</span>
                                <span class="value">
                            @foreach($product->tags as $tag)
                                        <span class="badge text-bg-light me-1" style="font-weight:500;font-size:.8rem">{{ $tag->name }}</span>
                                    @endforeach
                        </span>
                            </div>
                        @endif
                        @if($product->view)
                            <div class="info-row">
                                <span class="label"><i class="bi bi-eye me-2"></i>Lượt xem</span>
                                <span class="value">{{ number_format($product->view) }}</span>
                            </div>
                        @endif
                        @if($product->video)
                            <div class="info-row">
                                <span class="label"><i class="bi bi-play-circle me-2"></i>Video</span>
                                <span class="value">
                            <span class="badge text-bg-danger"><i class="bi bi-play-fill me-1"></i>Có video</span>
                        </span>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('admin.register') }}" class="btn btn-primary btn-lg px-5">
                            <i class="bi bi-rocket-takeoff me-2"></i>Đặt mua ngay
                        </a>
                        <a href="{{ route('frontend.product.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>

            {{-- TABS THÔNG TIN --}}
            <div class="mt-5 product-tabs">
                <ul class="nav nav-tabs" id="productTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">Mô tả</button>
                    </li>
                    @if($product->instructions)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="instr-tab" data-bs-toggle="tab" data-bs-target="#instr" type="button" role="tab">Hướng dẫn và khuyến cáo</button>
                        </li>
                    @endif
                    @if($product->licenses && count($product->licenses) > 0)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="license-tab" data-bs-toggle="tab" data-bs-target="#license" type="button" role="tab">Giấy phép</button>
                        </li>
                    @endif
                    @if($product->origin)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="origin-tab" data-bs-toggle="tab" data-bs-target="#origin" type="button" role="tab">Nguồn gốc</button>
                        </li>
                    @endif
                </ul>
                <div class="tab-content" id="productTabContent">
                    {{-- Mô tả --}}
                    <div class="tab-pane fade show active" id="desc" role="tabpanel">
                        <div class="detail-content">
                            {!! $product->content ?: '<p class="text-muted">Đang cập nhật...</p>' !!}
                        </div>
                    </div>

                    {{-- Hướng dẫn --}}
                    @if($product->instructions)
                        <div class="tab-pane fade" id="instr" role="tabpanel">
                            <div class="detail-content">
                                {!! $product->instructions !!}
                            </div>
                        </div>
                    @endif

                    {{-- Giấy phép --}}
                    @if($product->licenses && count($product->licenses) > 0)
                        <div class="tab-pane fade" id="license" role="tabpanel">
                            <div class="row g-3">
                                @foreach($product->licenses as $licImg)
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <a href="{{ cdn($licImg) }}" target="_blank">
                                            <img src="{{ cdn($licImg) }}" class="img-fluid rounded shadow-sm border" alt="Giấy phép">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Nguồn gốc --}}
                    @if($product->origin)
                        <div class="tab-pane fade" id="origin" role="tabpanel">
                            <div class="detail-content">
                                {{ $product->origin }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SẢN PHẨM LIÊN QUAN --}}
            @if($related->isNotEmpty())
                <div class="mt-5">
                    <h3 style="font-weight:700;font-size:1.2rem;margin-bottom:20px">
                        <i class="bi bi-grid me-2" style="color:var(--primary)"></i>Sản phẩm liên quan
                    </h3>
                    <div class="row g-3">
                        @foreach($related as $item)
                            <div class="col-6 col-md-3">
                                <div class="related-card">
                                    @if($item->image)
                                        <img src="{{ cdn($item->image) }}" alt="{{ $item->name }}">
                                    @else
                                        <div style="height:170px;background:var(--primary-light);display:flex;align-items:center;justify-content:center">
                                            <i class="bi bi-box-seam" style="font-size:2rem;color:var(--primary);opacity:.3"></i>
                                        </div>
                                    @endif
                                    <div style="padding:12px">
                                        @if($item->video)
                                            <span class="badge text-bg-danger mb-1" style="font-size:.7rem"><i class="bi bi-play-fill"></i> Video</span>
                                        @endif
                                        <div style="font-weight:600;font-size:.85rem;margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $item->name }}</div>
                                        <div style="color:#e74c3c;font-weight:700;font-size:.9rem">{{ number_format($item->price, 0, ',', '.') }}₫</div>
                                        <a href="{{ route('frontend.product.show', $item->slug) }}"
                                           class="btn btn-outline-primary btn-sm w-100 mt-2" style="font-size:.78rem">Xem chi tiết</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        (function () {
            const total = {{ count($mediaList) }};
            let current = 0;

            const main    = document.getElementById('galleryMain');
            const thumbBox = document.getElementById('galleryThumbs');

            function galleryGoto(idx) {
                if (!main) return;
                current = (idx + total) % total;

                /* Ẩn tất cả */
                main.querySelectorAll('img[data-index], .video-frame').forEach(el => {
                    el.classList.remove('active');
                    /* Dừng video khi chuyển sang slide khác */
                    if (el.classList.contains('video-frame')) {
                        const iframe = el.querySelector('iframe');
                        const video  = el.querySelector('video');
                        if (iframe) { const s = iframe.src; iframe.src = ''; iframe.src = s; }
                        if (video)  video.pause();
                    }
                });

                /* Hiển thị slide hiện tại */
                const target = main.querySelector('[data-index="'+current+'"]');
                if (target) target.classList.add('active');

                /* Cập nhật thumbnail */
                if (thumbBox) {
                    thumbBox.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
                    const activThumb = thumbBox.querySelector('.thumb[data-index="'+current+'"]');
                    if (activThumb) activThumb.classList.add('active');
                }
            }

            /* Expose toàn cục */
            window.galleryGoto = galleryGoto;
            window.galleryMove = function (dir) { galleryGoto(current + dir); };
        })();
    </script>
@endpush
