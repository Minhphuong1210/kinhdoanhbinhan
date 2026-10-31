@extends('Core::Frontend.layouts.app')

@section('title', 'Sản phẩm')

@section('extra-styles')
    <style>
        .page-hero {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: #fff; padding: 48px 0 40px; text-align: center;
        }
        .page-hero h1 { font-size: 2rem; font-weight: 800; }
        .page-hero .breadcrumb { justify-content: center; background: none; padding: 0; margin: 8px 0 0; }
        .page-hero .breadcrumb-item,
        .page-hero .breadcrumb-item a { color: rgba(255,255,255,.75); font-size: .875rem; text-decoration: none; }
        .page-hero .breadcrumb-item.active { color: #fff; }
        .page-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.5); }

        /* Filter bar */
        .filter-bar {
            background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,.06);
            padding: 14px 0; position: sticky; top: 120px; z-index: 100;
        }
        .filter-bar .cat-btn {
            display: inline-block; padding: 6px 16px; border-radius: 50px;
            border: 1.5px solid var(--border-light); font-size: .82rem; font-weight: 500;
            color: var(--text-dark); text-decoration: none; transition: all .2s; white-space: nowrap;
        }
        .filter-bar .cat-btn:hover,
        .filter-bar .cat-btn.active { background: var(--primary); border-color: var(--primary); color: #fff; }

        /* Product cards */
        .product-card {
            border: none; border-radius: 14px; overflow: hidden;
            box-shadow: 0 2px 14px rgba(0,0,0,.07);
            transition: transform .25s, box-shadow .25s;
            height: 100%; display: flex; flex-direction: column;
            background: #fff;
        }
        .product-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(0,0,0,.14); }

        .product-card .img-wrap {
            overflow: hidden;
            background: var(--primary-light); position: relative;
        }
        .product-card .img-wrap img {
            width: 100%; height: 100%; object-fit: cover; transition: transform .4s;
        }
        .product-card:hover .img-wrap img { transform: scale(1.07); }

        /* Overlay "Xem chi tiết" */
        .product-card .img-overlay {
            position: absolute; inset: 0;
            background: rgba(26,107,60,.55);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: opacity .3s;
        }
        .product-card:hover .img-overlay { opacity: 1; }
        .product-card .img-overlay span {
            color: #fff; font-weight: 700; font-size: .9rem;
            border: 2px solid #fff; padding: 8px 20px; border-radius: 50px;
        }

        /* Badges */
        .badge-sale {
            position: absolute; top: 10px; left: 10px;
            background: #e74c3c; color: #fff;
            font-size: .7rem; font-weight: 700; padding: 3px 10px; border-radius: 50px;
        }
        .badge-video {
            position: absolute; top: 10px; right: 10px;
            background: rgba(0,0,0,.6); color: #fff;
            font-size: .7rem; font-weight: 700; padding: 3px 10px; border-radius: 50px;
            display: flex; align-items: center; gap: 4px;
        }

        .product-card .card-body {
            padding: 14px 16px 16px; display: flex; flex-direction: column; flex: 1;
        }
        .product-card .cat-badge {
            font-size: .7rem; color: var(--primary); font-weight: 600;
            text-transform: uppercase; letter-spacing: .03em;
            margin-bottom: 5px;
        }
        .product-card .product-name {
            font-weight: 700; font-size: .9rem; color: var(--text-dark);
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
            overflow: hidden; flex: 1; margin-bottom: 10px; line-height: 1.45;
        }
        .product-card .prices { margin-bottom: 12px; }
        .product-card .price-new { color: #e74c3c; font-weight: 800; font-size: 1.05rem; }
        .product-card .price-old { color: #aaa; font-size: .8rem; text-decoration: line-through; margin-left: 6px; }
        .product-card .btn-detail {
            display: block; text-align: center;
            background: var(--primary-light); color: var(--primary);
            font-weight: 600; font-size: .85rem; padding: 8px;
            border-radius: 8px; text-decoration: none; transition: background .2s, color .2s;
        }
        .product-card .btn-detail:hover { background: var(--primary); color: #fff; }

        /* No image placeholder */
        .no-img-placeholder {
            height: 100%; display: flex; align-items: center; justify-content: center;
            background: var(--primary-light);
        }
    </style>
@endsection

@section('content')

    {{-- PAGE HERO --}}
    <div class="page-hero">
        <div class="container">
            <h1>Sản phẩm của chúng tôi</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Sản phẩm</li>
                </ol>
            </nav>
        </div>
    </div>


    <section style="padding: 40px 0 64px">
        <div class="container">




            @foreach($tags as $tag)
                @if($tag->products->count())

                    <div style="margin-bottom:50px">

                        {{-- HEADER TAG --}}
                        <div class="mb-3">
                            <h4 style="font-weight:700">{{ $tag->name }}</h4>
                        </div>

                        {{-- SLIDER --}}
                        <div class="swiper mySwiper-{{ $tag->id }}">
                            <div class="swiper-wrapper">

                                @foreach($tag->products as $product)
                                    <div class="swiper-slide" style="width:260px">

                                        <a href="{{ route('frontend.product.show', $product->slug) }}" style="text-decoration:none">
                                            <div class="product-card">

                                                <div class="img-wrap">

                                                    <div class="img-overlay">
                                                        <span><i class="bi bi-eye me-1"></i>Xem chi tiết</span>
                                                    </div>

                                                    @if($product->old_price && $product->old_price > $product->price)
                                                        <span class="badge-sale">
                                                -{{ round(($product->old_price - $product->price) / $product->old_price * 100) }}%
                                            </span>
                                                    @endif

                                                    @if($product->video)
                                                        <span class="badge-video">
                                                <i class="bi bi-play-circle-fill"></i> Video
                                            </span>
                                                    @endif

                                                    @if($product->image)
                                                        <img src="{{ cdn($product->image) }}" loading="lazy">
                                                    @else
                                                        <div class="no-img-placeholder">
                                                            <i class="bi bi-box-seam" style="font-size:3rem;color:var(--primary);opacity:.3"></i>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="card-body">
                                                    @if($product->category)
                                                        <div class="cat-badge">{{ $product->category->name }}</div>
                                                    @endif

                                                    <div class="product-name">{{ $product->name }}</div>

                                                    <div class="prices">
                                            <span class="price-new">
                                                {{ number_format($product->price, 0, ',', '.') }}₫
                                            </span>

                                                        @if($product->old_price)
                                                            <span class="price-old">
                                                    {{ number_format($product->old_price, 0, ',', '.') }}₫
                                                </span>
                                                        @endif
                                                    </div>

                                                    <span class="btn-detail">
                                            <i class="bi bi-eye me-1"></i>Xem chi tiết
                                        </span>
                                                </div>

                                            </div>
                                        </a>

                                    </div>
                                @endforeach

                            </div>
                        </div>

                        <div class="d-flex justify-content-center mt-3 gap-2">
                            <button class="btn btn-sm btn-light prev-{{ $tag->id }}">←</button>
                            <button class="btn btn-sm btn-light next-{{ $tag->id }}">→</button>
                        </div>

                    </div>

                @endif
            @endforeach
        </div>
    </section>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>


    <script>
        @foreach($tags as $tag)
        new Swiper(".mySwiper-{{ $tag->id }}", {
            slidesPerView: "auto",
            spaceBetween: 16,

            loop: true, // chạy vòng

            autoplay: {
                delay: 3000, // 3 giây
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            },

            speed: 600, // mượt hơn

            grabCursor: true, // kéo chuột được

            navigation: {
                nextEl: ".next-{{ $tag->id }}",
                prevEl: ".prev-{{ $tag->id }}",
            },
        });
        @endforeach
    </script>

@endsection
