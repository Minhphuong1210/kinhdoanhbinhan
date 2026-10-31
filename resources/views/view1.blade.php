@extends('Core::Frontend.layouts.app')

@section('title', 'Trang chủ')

@section('extra-styles')
    <style>
        /* ===== BANNER CAROUSEL ===== */
        .hero-carousel .carousel-item {
            height: 480px;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 60%, #2ecc71 100%);
        }

        .hero-carousel .carousel-caption {
            bottom: auto;
            top: 50%;
            transform: translateY(-50%);
            text-align: left;
            left: 8%;
            right: 40%;
        }

        .hero-carousel .carousel-caption h1 {
            font-size: 2.4rem;
            font-weight: 800;
            line-height: 1.25;
            text-shadow: 0 2px 12px rgba(0, 0, 0, .3);
        }

        .hero-carousel .carousel-caption p {
            font-size: 1rem;
            opacity: .9;
            margin-bottom: 1.5rem;
        }

        .hero-carousel .carousel-img-wrap {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 40%;
            overflow: hidden;
        }

        .hero-carousel .carousel-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: .25;
        }

        .hero-carousel .carousel-indicators button {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        /* ===== SECTIONS COMMON ===== */
        .section-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-dark);
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 50px;
            height: 4px;
            background: var(--accent);
            border-radius: 2px;
        }

        .section-wrap {
            padding: 64px 0;
        }

        .section-wrap.bg-soft {
            background: var(--primary-light);
        }

        /* ===== GIỚI THIỆU ===== */
        .about-img-wrap {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 16px 48px rgba(0, 0, 0, .12);
            position: relative;
        }

        .about-img-wrap img {
            width: 100%;
            /*height: 380px;*/
            object-fit: unset;
        }

        .about-badge {
            position: absolute;
            bottom: 16px;
            left: 16px;
            background: var(--accent);
            color: #fff;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 700;
            font-size: .9rem;
        }

        .stat-box {
            padding: 20px;
            border-radius: 12px;
            border: 1.5px solid var(--border-light);
            text-align: center;
            transition: box-shadow .2s, transform .2s;
        }

        .stat-box:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, .1);
            transform: translateY(-3px);
        }

        .stat-box h3 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 2px;
        }

        /* ===== CÂU CHUYỆN ===== */
        .story-timeline {
            position: relative;
            padding-left: 32px;
        }

        .story-timeline::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--primary-light);
        }

        .story-item {
            position: relative;
            margin-bottom: 36px;
        }

        .story-item::before {
            content: '';
            position: absolute;
            left: -27px;
            top: 5px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--primary);
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px var(--primary-light);
        }

        .story-year {
            font-size: .75rem;
            font-weight: 700;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ===== PRODUCT CARDS ===== */
        .product-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .07);
            transition: transform .25s, box-shadow .25s;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, .13);
        }

        .product-card .img-wrap {
            /*height: 200px;*/
            overflow: hidden;
            background: var(--primary-light);
        }

        .product-card .img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: unset;
            transition: transform .35s;
        }

        .product-card:hover .img-wrap img {
            transform: scale(1.06);
        }

        .product-card .card-body {
            padding: 14px 16px 16px;
        }

        .product-card .product-name {
            font-weight: 600;
            font-size: .9rem;
            color: var(--text-dark);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .product-card .price-new {
            color: #e74c3c;
            font-weight: 700;
            font-size: 1rem;
        }

        .product-card .price-old {
            color: var(--text-muted);
            font-size: .8rem;
            text-decoration: line-through;
            margin-left: 6px;
        }

        .badge-sale {
            background: #e74c3c;
            color: #fff;
            font-size: .7rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
        }

        /* ===== TOP SẢN PHẨM ===== */
        .top-product-item {
            display: flex;
            gap: 14px;
            align-items: center;
            padding: 14px;
            border-radius: 10px;
            transition: background .2s;
        }

        .top-product-item:hover {
            background: var(--primary-light);
        }

        .top-rank {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--border-light);
            min-width: 30px;
            text-align: center;
            line-height: 1;
        }

        .top-rank.rank-1 {
            color: #FFD700;
        }

        .top-rank.rank-2 {
            color: #C0C0C0;
        }

        .top-rank.rank-3 {
            color: #CD7F32;
        }

        .top-product-img {
            width: 64px;
            height: 64px;
            border-radius: 8px;
            object-fit: cover;
            background: var(--primary-light);
            flex-shrink: 0;
        }

        /* ===== MÔ HÌNH KINH DOANH ===== */
        .bizmodel-card {
            text-align: center;
            padding: 32px 20px;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .07);
            transition: transform .25s, box-shadow .25s;
            height: 100%;
        }

        .bizmodel-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 36px rgba(0, 0, 0, .13);
        }

        .bizmodel-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--primary);
            margin: 0 auto 20px;
        }

        .bizmodel-card h5 {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .bizmodel-card p {
            font-size: .875rem;
            color: var(--text-muted);
        }

        /* ===== TESTIMONIALS ===== */
        .testimonial-card {
            background: #fff;
            border-radius: 16px;
            padding: 28px 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .08);
            height: 100%;
        }

        .testimonial-card .stars {
            color: var(--accent);
            font-size: .9rem;
        }

        .testimonial-card .content {
            font-size: .9rem;
            line-height: 1.7;
            color: #444;
            margin: 14px 0;
        }

        .testimonial-card .author-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }
        .mo_hinh_kinh_doanh_chu{
            font-size: 20px !important;
        }

        .badge-hot {
            position: absolute;
            top: 10px;
            left: 10px;
            background: linear-gradient(45deg, red, orange);
            color: #fff;
            padding: 4px 10px;
            font-size: .7rem;
            border-radius: 20px;
        }

        /* ===== GIAO DIỆN MỚI TRANG CHỦ ===== */
        .home-new-page {
            background: linear-gradient(180deg, #f8fbf7 0%, #ffffff 100%);
        }

        .home-new-hero {
            background: linear-gradient(135deg, #0f5132 0%, #1f7a45 45%, #2bb673 100%);
            color: #fff;
            padding: 90px 0;
            position: relative;
            overflow: hidden;
        }

        .home-new-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255,255,255,.25), transparent 32%);
            pointer-events: none;
        }

        .home-new-pill {
            display: inline-flex;
            align-items: center;
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.24);
            color: #fff;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            backdrop-filter: blur(8px);
        }

        .home-new-hero h1 {
            font-size: clamp(2rem, 4vw, 3.1rem);
            font-weight: 800;
            line-height: 1.2;
            margin: 18px 0 16px;
        }

        .home-new-hero p {
            font-size: 1rem;
            line-height: 1.8;
            color: rgba(255,255,255,.9);
            max-width: 600px;
        }

        .home-new-card {
            border: 1px solid rgba(15, 81, 50, .08);
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
            padding: 24px;
            height: 100%;
        }

        .home-new-card .icon-box {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, #eaf8f0, #dff5e8);
            color: #1f7a45;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 16px;
        }

        .home-new-card h4 {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .home-new-card p {
            font-size: .92rem;
            color: var(--text-muted);
            margin-bottom: 0;
            line-height: 1.7;
        }

        .home-new-banner {
            background: linear-gradient(135deg, #f4fbf6 0%, #e5f7eb 100%);
            border-radius: 24px;
            padding: 32px;
            border: 1px solid rgba(31, 122, 69, .13);
        }

        .home-new-banner h3 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f5132;
            margin-bottom: 10px;
        }

        .home-new-banner p {
            color: #4f6f5a;
            line-height: 1.8;
            margin-bottom: 0;
        }

        .home-new-category-card {
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 10px 26px rgba(0, 0, 0, .06);
            height: 100%;
        }

        .home-new-category-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .home-new-category-card .content {
            padding: 18px 20px 20px;
        }

        .home-new-category-card .tag {
            display: inline-block;
            background: #f0fdf4;
            color: #1f7a45;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .home-new-cert-card {
            border-radius: 18px;
            background: #fff;
            padding: 22px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .05);
            height: 100%;
            border: 1px solid rgba(15, 81, 50, .08);
        }

        .home-new-cert-card .cert-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0f5132, #2bb673);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 14px;
        }

        .home-new-product-card {
            border: none;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
            height: 100%;
            transition: transform .2s ease, box-shadow .2s ease;
            background: #fff;
        }

        .home-new-product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, .1);
        }

        .home-new-product-card .img-wrap {
            height: 220px;
            background: linear-gradient(135deg, #f3fbf5, #e6f7eb);
            overflow: hidden;
        }

        .home-new-product-card .img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .home-new-product-card .card-body {
            padding: 18px;
        }

        .home-new-product-card .product-name {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .home-new-product-card .price {
            color: #e74c3c;
            font-weight: 800;
            font-size: 1rem;
        }

        .home-new-product-card .old-price {
            color: var(--text-muted);
            text-decoration: line-through;
            font-size: .84rem;
            margin-left: 6px;
        }

        .home-new-section-title {
            margin-bottom: 24px;
        }

        .home-new-section-title .eyebrow {
            color: #1f7a45;
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .home-new-section-title h3 {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .home-new-section-title p {
            color: var(--text-muted);
            margin-bottom: 0;
            max-width: 700px;
            line-height: 1.75;
        }

        .home-about-card {
            background: linear-gradient(135deg, #ffffff 0%, #f7fcf8 100%);
            border: 1px solid rgba(31, 122, 69, .12);
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 16px 40px rgba(0, 0, 0, .06);
        }

        .home-about-media {
            border-radius: 20px;
            overflow: hidden;
            min-height: 320px;
            background: linear-gradient(135deg, #eef9f1, #dff4e6);
        }

        .home-about-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .home-about-placeholder {
            width: 100%;
            height: 100%;
            min-height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1f7a45;
            font-size: 3rem;
            background: linear-gradient(135deg, #eef9f1, #dff4e6);
        }

        .home-about-stat {
            background: #fff;
            border-radius: 16px;
            padding: 14px 12px;
            border: 1px solid rgba(31, 122, 69, .1);
            text-align: center;
            height: 100%;
        }

        .home-about-stat strong {
            display: block;
            color: #1f7a45;
            font-size: 1.2rem;
            font-weight: 800;
        }

        .home-about-stat span {
            display: block;
            color: var(--text-muted);
            font-size: .84rem;
            margin-top: 4px;
            line-height: 1.5;
        }

        @media (max-width: 767.98px) {
            .home-about-card {
                padding: 18px;
            }

            .home-about-media,
            .home-about-placeholder {
                min-height: 240px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="home-new-page">
        {{-- ===== BANNER CHÍNH ===== --}}
        <section class="home-new-hero">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-7">
                        <span class="home-new-pill"><i class="bi bi-stars me-2"></i>Thương hiệu uy tín</span>
                        <h1>Chất lượng sản phẩm, giá trị bền vững và dịch vụ tin cậy.</h1>
                        <p>Chúng tôi mang đến những sản phẩm được lựa chọn kỹ lưỡng, phù hợp cho người tiêu dùng và đối tác kinh doanh, cùng hệ thống hỗ trợ rõ ràng và minh bạch.</p>
                        <div class="d-flex flex-wrap gap-3 mt-4">
                            <a href="#products" class="btn btn-light btn-lg px-4 fw-bold">Xem sản phẩm</a>
                            <a href="{{ route('admin.register') }}" class="btn btn-outline-light btn-lg px-4">Đăng ký tham gia</a>
                        </div>
                        <div class="row g-3 mt-4">
                            <div class="col-sm-4">
                                <div class="home-new-card p-3">
                                    <div class="fw-bold fs-5 text-white-50">03+</div>
                                    <div class="small text-muted">Năm kinh doanh</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="home-new-card p-3">
                                    <div class="fw-bold fs-5 text-white-50">20K+</div>
                                    <div class="small text-muted">Khách hàng tin dùng</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="home-new-card p-3">
                                    <div class="fw-bold fs-5 text-white-50">50+</div>
                                    <div class="small text-muted">Mẫu sản phẩm</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="home-new-card p-4">
                            <div class="home-new-section-title">
                                <div class="eyebrow">Tại sao chọn chúng tôi</div>
                                <h3>Đồng hành cùng doanh nghiệp phát triển bền vững</h3>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-6">
                                    <div class="home-new-card p-3">
                                        <div class="icon-box"><i class="bi bi-shield-check"></i></div>
                                        <h4>Đảm bảo chất lượng</h4>
                                        <p>Sản phẩm được kiểm định và cập nhật liên tục.</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="home-new-card p-3">
                                        <div class="icon-box"><i class="bi bi-people"></i></div>
                                        <h4>Hỗ trợ nhanh</h4>
                                        <p>Đội ngũ tư vấn sẵn sàng xử lý mọi câu hỏi.</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="home-new-card p-3">
                                        <div class="icon-box"><i class="bi bi-diagram-3"></i></div>
                                        <h4>Mô hình rõ ràng</h4>
                                        <p>Minh bạch thông tin, quy trình và lợi ích.</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="home-new-card p-3">
                                        <div class="icon-box"><i class="bi bi-globe2"></i></div>
                                        <h4>Phát triển lâu dài</h4>
                                        <p>Tiếp cận thị trường và xây dựng giá trị bền vững.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== DANH MỤC SẢN PHẨM 1 ===== --}}
        <section class="section-wrap">
            <div class="container">
                <div class="home-new-section-title">
                    <div class="eyebrow">Danh mục sản phẩm 1</div>
                    <h3>Những nhóm sản phẩm được yêu thích nhất</h3>
                    <p>Khám phá các dòng sản phẩm phù hợp cho nhu cầu đời sống, sức khỏe và phát triển kinh doanh.</p>
                </div>
                <div class="row g-4">
                    @php
                        $categoriesOne = [
                            ['title' => 'Sản phẩm chăm sóc sức khỏe', 'desc' => 'Lựa chọn an toàn cho gia đình và cộng đồng.', 'image' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=900&q=80', 'tag' => 'Mới'],
                            ['title' => 'Sản phẩm dinh dưỡng', 'desc' => 'Bổ sung dinh dưỡng và hỗ trợ sức khỏe lâu dài.', 'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=900&q=80', 'tag' => 'Hot'],
                            ['title' => 'Sản phẩm làm đẹp', 'desc' => 'Giải pháp phù hợp cho nhu cầu chăm sóc bản thân.', 'image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=900&q=80', 'tag' => 'Bán chạy'],
                            ['title' => 'Sản phẩm công nghệ', 'desc' => 'Tiện ích và giải pháp hiện đại cho cuộc sống.', 'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=900&q=80', 'tag' => 'Mới'],
                        ];
                    @endphp
                    @foreach($categoriesOne as $item)
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="home-new-category-card">
                                <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}">
                                <div class="content">
                                    <span class="tag">{{ $item['tag'] }}</span>
                                    <h4 class="fw-bold mb-2">{{ $item['title'] }}</h4>
                                    <p class="text-muted mb-0">{{ $item['desc'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ===== BANNER GIỮA ===== --}}
        <section class="section-wrap pt-0">
            <div class="container">
                <div class="home-new-banner d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
                    <div>
                        <div class="eyebrow">Ưu đãi nổi bật</div>
                        <h3>Đa dạng sản phẩm, giá trị rõ ràng và hỗ trợ tốt nhất.</h3>
                        <p>Đây là không gian dành cho các sản phẩm mới, mô hình phân phối minh bạch và cơ hội hợp tác lâu dài.</p>
                    </div>
                    <a href="#products" class="btn btn-success btn-lg px-4 fw-bold">Khám phá ngay</a>
                </div>
            </div>
        </section>

        {{-- ===== DANH MỤC SẢN PHẨM 2 ===== --}}
        <section class="section-wrap" style="background:#f8fbf8">
            <div class="container">
                <div class="home-new-section-title">
                    <div class="eyebrow">Danh mục sản phẩm 2</div>
                    <h3>Gợi ý theo nhu cầu và phong cách kinh doanh</h3>
                    <p>Một cách nhìn mới về sản phẩm giúp khách hàng và đối tác dễ dàng lựa chọn phù hợp.</p>
                </div>
                <div class="row g-4">
                    @php
                        $categoriesTwo = [
                            ['title' => 'Sản phẩm cho doanh nghiệp', 'desc' => 'Phù hợp cho đối tác, nhà phân phối và mô hình bán hàng.', 'icon' => 'bi-briefcase'],
                            ['title' => 'Sản phẩm cho gia đình', 'desc' => 'Những lựa chọn thiết thực, tiện lợi và đáng tin cậy.', 'icon' => 'bi-house-door'],
                            ['title' => 'Sản phẩm cho cộng đồng', 'desc' => 'Giải pháp dành cho các hoạt động và chương trình lớn.', 'icon' => 'bi-people-fill'],
                            ['title' => 'Sản phẩm cho thương hiệu', 'desc' => 'Tạo dấu ấn riêng cho doanh nghiệp và chiến dịch của bạn.', 'icon' => 'bi-award'],
                        ];
                    @endphp
                    @foreach($categoriesTwo as $item)
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="home-new-card">
                                <div class="icon-box"><i class="bi {{ $item['icon'] }}"></i></div>
                                <h4>{{ $item['title'] }}</h4>
                                <p>{{ $item['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ===== GIẤY CHỨNG NHẬN SẢN PHẨM ===== --}}
        <section class="section-wrap">
            <div class="container">
                <div class="home-new-section-title">
                    <div class="eyebrow">Giấy chứng nhận sản phẩm</div>
                    <h3>Minh bạch, tin cậy và chịu trách nhiệm với khách hàng</h3>
                    <p>Những tiêu chuẩn và chứng nhận giúp sản phẩm luôn giữ được sự tin tưởng từ người dùng.</p>
                </div>
                <div class="row g-4">
                    @php
                        $certs = [
                            ['title' => 'Chứng nhận chất lượng', 'desc' => 'Đảm bảo quy trình kiểm soát chất lượng theo tiêu chuẩn rõ ràng.', 'icon' => 'bi-award-fill'],
                            ['title' => 'Chứng nhận nguồn gốc', 'desc' => 'Theo dõi và xác minh nguồn gốc sản phẩm từ đầu vào.', 'icon' => 'bi-box-seam'],
                            ['title' => 'Chứng nhận an toàn', 'desc' => 'Khẳng định sản phẩm đáp ứng tiêu chuẩn an toàn sử dụng.', 'icon' => 'bi-shield-fill-check'],
                            ['title' => 'Chứng nhận dịch vụ', 'desc' => 'Hỗ trợ khách hàng với quy trình xử lý chuyên nghiệp.', 'icon' => 'bi-headset'],
                        ];
                    @endphp
                    @foreach($certs as $cert)
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="home-new-cert-card">
                                <div class="cert-icon"><i class="bi {{ $cert['icon'] }}"></i></div>
                                <h4 class="fw-bold mb-2">{{ $cert['title'] }}</h4>
                                <p class="text-muted mb-0">{{ $cert['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ===== SẢN PHẨM ===== --}}
        <section class="section-wrap" id="products">
            <div class="container">
                <div class="home-new-section-title">
                    <div class="eyebrow">Sản phẩm nổi bật</div>
                    <h3>Danh sách sản phẩm mới cập nhật</h3>
                    <p>Giao diện được thiết kế để sản phẩm nổi bật rõ ràng, dễ nhìn và thu hút hơn.</p>
                </div>
                <div class="row g-4">
                    @forelse($products as $product)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="home-new-product-card card">
                                <div class="img-wrap">
                                    @if ($product->image)
                                        <img src="{{ cdn($product->image) }}" alt="{{ $product->name }}">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100">
                                            <i class="bi bi-image" style="font-size:2.2rem;color:#7aa887"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="product-name">{{ $product->name }}</div>
                                    <div class="d-flex align-items-center flex-wrap">
                                        <span class="price">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                                        @if ($product->old_price)
                                            <span class="old-price">{{ number_format($product->old_price, 0, ',', '.') }}₫</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('frontend.product.show', $product->slug) }}" class="btn btn-success btn-sm w-100 mt-3">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        @for ($i = 0; $i < 8; $i++)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="home-new-product-card card">
                                    <div class="img-wrap">
                                        <div class="d-flex align-items-center justify-content-center h-100">
                                            <i class="bi bi-box-seam" style="font-size:2.2rem;color:#7aa887"></i>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="product-name">Sản phẩm mẫu {{ $i + 1 }}</div>
                                        <span class="price">299.000₫</span>
                                        <a href="#" class="btn btn-success btn-sm w-100 mt-3">Xem chi tiết</a>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    @endforelse
                </div>
            </div>
        </section>

        {{-- ===== GIỚI THIỆU / CTA ===== --}}
        <section class="section-wrap pt-0">
            <div class="container">
                <div class="home-about-card">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-6">
                            <div class="home-about-media">
                                @if (!empty($about_us->image))
                                    <img src="{{ cdn($about_us->image) }}" alt="{{ $about_us->name ?? 'Về chúng tôi' }}">
                                @else
                                    <div class="home-about-placeholder">
                                        <i class="bi bi-building"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="eyebrow">Về chúng tôi</div>
                            <h3 class="mb-3">{{ $about_us ? $about_us->name : 'Giới thiệu về công ty' }}</h3>
                            <p class="mb-4" style="line-height:1.8; color:#4b5563;">
                                {!! Str::limit(strip_tags($about_us->content ?? 'Đang cập nhật nội dung...'), 600) !!}
                            </p>

                            <div class="row g-3 mb-4">
                                <div class="col-sm-4">
                                    <div class="home-about-stat">
                                        <strong>03+</strong>
                                        <span>Năm kinh doanh</span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="home-about-stat">
                                        <strong>20K+</strong>
                                        <span>Khách hàng</span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="home-about-stat">
                                        <strong>50+</strong>
                                        <span>Sản phẩm</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-3">
                                <a href="{{ route('admin.register') }}" class="btn btn-success btn-lg px-4 fw-bold">Tham gia ngay</a>
                                <a href="#products" class="btn btn-outline-success btn-lg px-4">Xem sản phẩm</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
