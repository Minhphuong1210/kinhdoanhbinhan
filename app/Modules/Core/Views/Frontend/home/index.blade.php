@extends('Core::Frontend.layouts.app')

@section('title', 'Trang chủ')

@php
    $aboutText = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $about_us?->content)))), 220);
    $stats = [
        ['value' => $about_us?->nam_kinh_doanh, 'label' => 'Năm kinh doanh', 'icon' => 'fa-award'],
        ['value' => $about_us?->khach_hang ?: number_format($homeStats['completed_order_count'] ?? 0, 0, ',', '.'), 'label' => $about_us?->khach_hang ? 'Khách hàng' : 'Đơn hàng thành công', 'icon' => 'fa-users'],
        ['value' => $about_us?->san_pham ?: number_format($homeStats['product_count'] ?? 0, 0, ',', '.'), 'label' => 'Sản phẩm', 'icon' => 'fa-box-open'],
    ];
    $stats = array_values(array_filter($stats, fn ($s) => !empty($s['value'])));
    $heroVideo = $site['video_kinhdoanhbinhan'] ?? $site['video_hoptacxa'] ?? null;
@endphp

@push('styles')
<style>
    .home-hero { background: radial-gradient(1200px 500px at 85% -10%, rgba(31,195,180,.22), transparent 60%), radial-gradient(900px 400px at -10% 110%, rgba(116,207,99,.20), transparent 60%), #fff; padding: 64px 0 56px; border-bottom: 1px solid var(--line); }
    .home-hero h1 { font-weight: 800; font-size: 2.6rem; line-height: 1.2; }
    .home-hero h1 span { background: var(--brand-gradient); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .home-hero .lead { color: var(--muted); font-size: 1.05rem; }
    .hero-media { position: relative; border-radius: 22px; overflow: hidden; box-shadow: 0 24px 60px rgba(16,48,45,.18); background: #0b2a29; aspect-ratio: 4 / 3; }
    .hero-media img, .hero-media video { width: 100%; height: 100%; object-fit: cover; display: block; }
    .hero-float { position: absolute; left: -18px; bottom: 26px; background: #fff; border-radius: 14px; padding: 12px 16px; box-shadow: var(--shadow); display: flex; gap: 10px; align-items: center; }
    .hero-float .ic { width: 40px; height: 40px; border-radius: 50%; background: var(--brand-gradient); color: #fff; display: grid; place-items: center; }
    .hero-stats { display: flex; gap: 28px; flex-wrap: wrap; margin-top: 30px; }
    .hero-stats .num { font-size: 1.7rem; font-weight: 800; color: var(--primary-dark); line-height: 1; }
    .hero-stats .lbl { color: var(--muted); font-size: 13px; margin-top: 4px; }

    .benefit-strip { margin-top: -30px; position: relative; z-index: 2; }
    .benefit-item { background: #fff; border-radius: var(--radius); padding: 18px; display: flex; gap: 14px; align-items: center; box-shadow: var(--shadow); height: 100%; }
    .benefit-item .ic { flex: 0 0 48px; height: 48px; border-radius: 12px; background: var(--primary-soft); color: var(--primary); display: grid; place-items: center; font-size: 20px; }
    .benefit-item strong { display: block; font-size: 15px; }
    .benefit-item small { color: var(--muted); }

    .cat-chip { display: flex; align-items: center; gap: 12px; background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 14px 16px; color: var(--ink); height: 100%; transition: .2s; }
    .cat-chip:hover { border-color: var(--primary); box-shadow: var(--shadow); color: var(--primary-dark); }
    .cat-chip .ic { width: 44px; height: 44px; border-radius: 50%; background: var(--brand-gradient); color: #fff; display: grid; place-items: center; flex: 0 0 44px; }
    .cat-chip strong { display: block; font-size: 14px; }
    .cat-chip small { color: var(--muted); }

    .about-block { background: #fff; border-radius: 24px; overflow: hidden; border: 1px solid var(--line); }
    .about-block .about-img { height: 100%; min-height: 320px; background-size: cover; background-position: center; }
    .about-block .about-body { padding: 40px; }

    .promo-banner { background: var(--brand-gradient); border-radius: 24px; color: #fff; padding: 40px; position: relative; overflow: hidden; }
    .promo-banner::after { content: ''; position: absolute; right: -60px; bottom: -60px; width: 240px; height: 240px; border-radius: 50%; background: rgba(255,255,255,.14); }
    .promo-banner h3 { font-weight: 800; }

    .leader-card { text-align: center; background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 22px 16px; height: 100%; }
    .leader-card img { width: 96px; height: 96px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-soft); }
    .testimonial { background: #fff; border-radius: var(--radius); padding: 24px; border: 1px solid var(--line); height: 100%; }
    .testimonial .quote { color: var(--primary); font-size: 26px; }

    .scroll-row { display: grid; grid-auto-flow: column; grid-auto-columns: calc(25% - 18px); gap: 24px; overflow-x: auto; scroll-snap-type: x mandatory; padding-bottom: 8px; }
    .scroll-row > * { scroll-snap-align: start; }
    @media (max-width: 991.98px) {
        .home-hero { padding: 40px 0 48px; }
        .home-hero h1 { font-size: 1.8rem; }
        .hero-float { left: 10px; }
        .about-block .about-body { padding: 24px; }
        .scroll-row { grid-auto-columns: 46%; gap: 14px; }
    }
    @media (max-width: 575.98px) {
        .home-hero h1 { font-size: 1.55rem; }
        .benefit-strip { margin-top: -24px; }
        .benefit-item { padding: 12px; gap: 10px; }
        .benefit-item .ic { flex-basis: 38px; height: 38px; font-size: 16px; }
        .benefit-item strong { font-size: 13.5px; }
        .benefit-item small { font-size: 12px; }
        /* Danh mục: icon nằm trên chữ để không bị bóp chữ */
        .cat-chip { flex-direction: column; text-align: center; gap: 8px; padding: 14px 8px; }
        .cat-chip strong { font-size: 13px; }
    }
</style>
@endpush

@section('content')

{{-- ================= HERO ================= --}}
@if($heroBanners->isNotEmpty())
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach($heroBanners as $i => $banner)
                <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                    <a href="{{ $banner->link ?: '#' }}">
                        <img src="{{ cdn($banner->image) }}" class="d-block w-100" alt="{{ $banner->title ?: $banner->name }}" style="max-height:520px;object-fit:cover">
                    </a>
                </div>
            @endforeach
        </div>
        @if($heroBanners->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
        @endif
    </div>
@endif

<section class="home-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="eyebrow"><i class="fas fa-leaf me-1"></i> Sức khỏe từ thiên nhiên</span>
                <h1>{{ $site['name'] ?? 'Bình An' }} – <span>Sống khỏe, kinh doanh bền vững</span></h1>
                <p class="lead mt-3">{{ $aboutText ?: 'Cung cấp sản phẩm dinh dưỡng, hàng tiêu dùng chất lượng, nguồn gốc rõ ràng cho mọi gia đình Việt.' }}</p>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="{{ route('frontend.product.index') }}" class="btn btn-brand btn-lg"><i class="fas fa-shopping-bag me-2"></i>Khám phá sản phẩm</a>
                    @guest
                        <a href="{{ route('admin.register') }}" class="btn btn-outline-brand btn-lg">Đăng ký thành viên</a>
                    @else
                        <a href="{{ route('frontend.about') }}" class="btn btn-outline-brand btn-lg">Về chúng tôi</a>
                    @endguest
                </div>
                @if($stats)
                    <div class="hero-stats">
                        @foreach($stats as $stat)
                            <div><div class="num">{{ $stat['value'] }}</div><div class="lbl"><i class="fas {{ $stat['icon'] }} me-1 text-brand"></i>{{ $stat['label'] }}</div></div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-lg-6">
                <div class="position-relative">
                    <div class="hero-media">
                        @if($heroVideo)
                            <video src="{{ cdn($heroVideo) }}" controls playsinline preload="metadata" poster="{{ $about_us?->image ? cdn($about_us->image) : '' }}"></video>
                        @elseif($about_us?->image)
                            <img src="{{ cdn($about_us->image) }}" alt="{{ $about_us->name }}">
                        @elseif($topProducts->first()?->image)
                            <img src="{{ cdn($topProducts->first()->image) }}" alt="">
                        @endif
                    </div>
                    <div class="hero-float d-none d-sm-flex">
                        <span class="ic"><i class="fas fa-shield-alt"></i></span>
                        <span><strong class="d-block">Chính hãng 100%</strong><small class="text-muted">Nguồn gốc rõ ràng</small></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= LỢI ÍCH ================= --}}
<section class="benefit-strip">
    <div class="container">
        <div class="row g-3">
            @foreach([
                ['fa-certificate', 'Sản phẩm chính hãng', 'Đầy đủ giấy tờ, chứng nhận'],
                ['fa-truck-fast', 'Giao hàng toàn quốc', 'Theo dõi đơn dễ dàng'],
                ['fa-rotate-left', 'Đổi trả rõ ràng', 'Theo chính sách công khai'],
                ['fa-headset', 'Hỗ trợ tận tâm', $site['hotline'] ?? 'Tư vấn miễn phí'],
            ] as [$icon, $title, $desc])
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="benefit-item">
                        <span class="ic"><i class="fas {{ $icon }}"></i></span>
                        <span><strong>{{ $title }}</strong><small>{{ $desc }}</small></span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= DANH MỤC ================= --}}
@if($productCategories->isNotEmpty())
<section class="section pb-0">
    <div class="container">
        <div class="section-title">
            <div><span class="eyebrow">Danh mục</span><h2>Mua sắm theo <span>danh mục</span></h2></div>
            <a href="{{ route('frontend.product.index') }}" class="btn btn-outline-brand btn-sm">Tất cả sản phẩm</a>
        </div>
        <div class="row g-3">
            @foreach($productCategories as $cat)
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('frontend.product.index', ['category' => $cat->slug]) }}" class="cat-chip">
                        <span class="ic"><i class="fas fa-seedling"></i></span>
                        <span><strong>{{ $cat->name }}</strong><small>{{ $cat->products_count }} sản phẩm</small></span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ================= SẢN PHẨM NỔI BẬT ================= --}}
@if($topProducts->isNotEmpty())
<section class="section">
    <div class="container">
        <div class="section-title">
            <div><span class="eyebrow">Bán chạy</span><h2>Sản phẩm <span>nổi bật</span></h2><p>Được khách hàng tin dùng và lựa chọn nhiều nhất</p></div>
            <a href="{{ route('frontend.product.index', ['sort' => 'xem-nhieu']) }}" class="btn btn-outline-brand btn-sm d-none d-sm-inline-block">Xem thêm</a>
        </div>
        <div class="row g-3 g-lg-4">
            @foreach($topProducts as $product)
                <div class="col-6 col-md-4 col-lg-3">@include('Core::Frontend.home.partials.product-card', ['product' => $product])</div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ================= VỀ CHÚNG TÔI ================= --}}
@if($about_us)
<section class="section pt-0">
    <div class="container">
        <div class="about-block">
            <div class="row g-0">
                <div class="col-lg-5">
                    <div class="about-img" style="background-image:url('{{ $about_us->image ? cdn($about_us->image) : $site['logo_url'] }}')"></div>
                </div>
                <div class="col-lg-7">
                    <div class="about-body">
                        <span class="eyebrow">Về chúng tôi</span>
                        <h2 class="fw-bold mb-3">{{ $about_us->name }}</h2>
                        <p class="text-muted" style="line-height:1.8">{{ \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $about_us->content)))), 520) }}</p>
                        <div class="row g-3 my-2">
                            @foreach(['Sản phẩm có nguồn gốc rõ ràng', 'Chính sách bán hàng minh bạch', 'Đồng hành cùng cộng đồng', 'Chăm sóc khách hàng tận tâm'] as $point)
                                <div class="col-sm-6"><i class="fas fa-circle-check text-brand me-2"></i>{{ $point }}</div>
                            @endforeach
                        </div>
                        <a href="{{ route('frontend.about') }}" class="btn btn-brand mt-3">Tìm hiểu thêm <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ================= SẢN PHẨM THEO DANH MỤC ================= --}}
@foreach($categorySections as $section)
<section class="section {{ $loop->odd ? 'bg-white' : '' }}">
    <div class="container">
        <div class="section-title">
            <div><h2>{{ $section->name }}</h2></div>
            <a href="{{ route('frontend.product.index', ['category' => $section->slug]) }}" class="btn btn-outline-brand btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="scroll-row">
            @foreach($section->homeProducts as $product)
                @include('Core::Frontend.home.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endforeach

{{-- ================= ƯU ĐÃI / COMBO ================= --}}
@if($comboProducts->isNotEmpty())
<section class="section">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-4">
                <div class="promo-banner h-100 d-flex flex-column justify-content-center">
                    <span class="badge bg-white text-dark align-self-start mb-3">Ưu đãi</span>
                    <h3>Combo tiết kiệm cho cả gia đình</h3>
                    <p class="mb-4" style="opacity:.9">Mua theo combo hoặc tự chọn sản phẩm yêu thích với mức giá tốt hơn.</p>
                    <a href="{{ route('frontend.product.index', ['q' => 'combo']) }}" class="btn btn-light fw-semibold align-self-start">Xem combo</a>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row g-3">
                    @foreach($comboProducts->take(6) as $product)
                        <div class="col-6 col-md-4">@include('Core::Frontend.home.partials.product-card', ['product' => $product])</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ================= ĐỘI NGŨ ================= --}}
@if($leaders->isNotEmpty())
<section class="section bg-white">
    <div class="container">
        <div class="section-title justify-content-center text-center">
            <div><span class="eyebrow">Đội ngũ</span><h2>Ban <span>lãnh đạo</span></h2></div>
        </div>
        <div class="row g-3 justify-content-center">
            @foreach($leaders->take(4) as $leader)
                <div class="col-6 col-md-3">
                    <div class="leader-card">
                        @if($leader->image)<img src="{{ cdn($leader->image) }}" alt="{{ $leader->name }}" loading="lazy">@endif
                        <h6 class="fw-bold mt-3 mb-1">{{ $leader->name }}</h6>
                        <small class="text-muted">{{ $leader->chucvu }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ================= CẢM NHẬN KHÁCH HÀNG ================= --}}
@if($cam_nhan->isNotEmpty())
<section class="section">
    <div class="container">
        <div class="section-title justify-content-center text-center">
            <div><span class="eyebrow">Khách hàng nói gì</span><h2>Cảm nhận <span>khách hàng</span></h2></div>
        </div>
        <div class="row g-3">
            @foreach($cam_nhan->take(3) as $review)
                <div class="col-md-4">
                    <div class="testimonial">
                        <div class="quote"><i class="fas fa-quote-left"></i></div>
                        <div class="text-muted my-2">{{ \Illuminate\Support\Str::limit(html_entity_decode(strip_tags((string) $review->content)), 260) }}</div>
                        <div class="d-flex align-items-center gap-2 mt-3">
                            @if($review->image)<img src="{{ cdn($review->image) }}" alt="" style="width:44px;height:44px;border-radius:50%;object-fit:cover">@endif
                            <strong>{{ $review->title ?: $review->name }}</strong>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ================= TIN TỨC ================= --}}
@if($posts->isNotEmpty())
<section class="section bg-white">
    <div class="container">
        <div class="section-title">
            <div><span class="eyebrow">Tin tức</span><h2>Bài viết <span>mới nhất</span></h2></div>
            <a href="{{ route('frontend.news.index') }}" class="btn btn-outline-brand btn-sm">Tất cả bài viết</a>
        </div>
        <div class="row g-4">
            @foreach($posts->take(3) as $post)
                <div class="col-md-4">@include('Core::Frontend.news.partials.post-card', ['post' => $post])</div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
