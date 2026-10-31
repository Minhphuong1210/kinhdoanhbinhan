@php
    $siteName   = $site['name'] ?? config('app.name');
    $pageTitle  = trim($__env->yieldContent('title'));
    $metaDesc   = trim($__env->yieldContent('meta_description')) ?: ($site['default_meta_description'] ?? $siteName);
    $hotlineRaw = preg_replace('/[^0-9+]/', '', $site['hotline'] ?? '');
@endphp
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ? $pageTitle . ' | ' . $siteName : ($site['default_meta_title'] ?? $siteName) }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($metaDesc), 160) }}">
    @if(!empty($site['default_meta_keywords']))<meta name="keywords" content="{{ $site['default_meta_keywords'] }}">@endif
    <meta property="og:title" content="{{ $pageTitle ?: $siteName }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($metaDesc), 160) }}">
    <meta property="og:image" content="@yield('og_image', $site['logo_url'])">
    <meta property="og:type" content="website">
    <link rel="icon" href="{{ $site['favicon_url'] }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            /* Màu lấy theo logo: lá xanh → ngọc → xanh dương */
            --primary: #0c9a8a;
            --primary-dark: #08686d;
            --primary-soft: #e7f7f4;
            --accent: #1769d6;
            --leaf: #74cf63;
            --brand-gradient: linear-gradient(120deg, #74cf63 0%, #1fc3b4 48%, #1769d6 100%);
            --ink: #10302d;
            --muted: #5d716e;
            --line: #e2ecea;
            --bg: #f5f9f8;
            --danger: #e2483d;
            --radius: 14px;
            --shadow: 0 6px 24px rgba(16, 48, 45, .08);
        }

        * { box-sizing: border-box; }
        body { font-family: 'Be Vietnam Pro', system-ui, sans-serif; color: var(--ink); background: var(--bg); font-size: 15px; }
        a { color: var(--primary-dark); text-decoration: none; }
        a:hover { color: var(--primary); }
        img { max-width: 100%; }

        .btn-brand { background: var(--primary); border: 0; color: #fff; font-weight: 600; border-radius: 10px; padding: .6rem 1.2rem; }
        .btn-brand:hover { background: var(--primary-dark); color: #fff; }
        .btn-outline-brand { border: 1.5px solid var(--primary); color: var(--primary-dark); font-weight: 600; border-radius: 10px; padding: .55rem 1.1rem; background: #fff; }
        .btn-outline-brand:hover { background: var(--primary); color: #fff; }
        .text-brand { color: var(--primary) !important; }
        .bg-brand-soft { background: var(--primary-soft); }

        /* ---------- Top bar ---------- */
        .topbar { background: var(--primary-dark); color: rgba(255,255,255,.85); font-size: 13px; }
        .topbar a { color: rgba(255,255,255,.9); }
        .topbar a:hover { color: #fff; }

        /* ---------- Header ---------- */
        .site-header { background: #fff; border-bottom: 1px solid var(--line); position: sticky; top: 0; z-index: 1030; }
        .brand-logo { display: flex; align-items: center; gap: 10px; color: var(--ink); }
        .brand-logo img { height: 48px; width: auto; }
        .brand-logo .brand-name { font-weight: 800; font-size: 15px; line-height: 1.2; max-width: 230px; }
        .brand-logo .brand-name small { display: block; font-weight: 500; color: var(--muted); font-size: 11.5px; }
        .header-search { position: relative; flex: 1; max-width: 460px; }
        .header-search input { border-radius: 999px; padding: .6rem 3rem .6rem 1.1rem; border: 1.5px solid var(--line); background: var(--bg); }
        .header-search input:focus { border-color: var(--primary); box-shadow: 0 0 0 .2rem rgba(12,154,138,.15); background: #fff; }
        .header-search button { position: absolute; right: 5px; top: 50%; transform: translateY(-50%); border: 0; background: var(--primary); color: #fff; width: 36px; height: 36px; border-radius: 50%; }
        .header-hotline { display: flex; align-items: center; gap: 10px; color: var(--ink); }
        .header-hotline .icon { width: 40px; height: 40px; border-radius: 50%; background: var(--primary-soft); color: var(--primary); display: grid; place-items: center; }
        .header-hotline small { display: block; color: var(--muted); font-size: 12px; }
        .header-hotline strong { color: var(--danger); }

        .main-nav { background: #fff; border-top: 1px solid var(--line); }
        .main-nav .nav-link { color: var(--ink); font-weight: 600; padding: .85rem 1rem; position: relative; }
        .main-nav .nav-link:hover, .main-nav .nav-link.active { color: var(--primary); }
        .main-nav .nav-link.active::after { content: ''; position: absolute; left: 1rem; right: 1rem; bottom: 0; height: 3px; border-radius: 3px; background: var(--brand-gradient); }
        .main-nav .dropdown-menu { border: 0; box-shadow: var(--shadow); border-radius: 12px; padding: .5rem; min-width: 240px; }
        .main-nav .dropdown-item { border-radius: 8px; padding: .55rem .8rem; font-weight: 500; }
        .main-nav .dropdown-item:hover { background: var(--primary-soft); color: var(--primary-dark); }

        /* ---------- Chung ---------- */
        .section { padding: 56px 0; }
        .section-title { display: flex; align-items: end; justify-content: space-between; gap: 16px; margin-bottom: 26px; }
        .section-title h2 { font-weight: 800; font-size: 1.6rem; margin: 0; }
        .section-title h2 span { background: var(--brand-gradient); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .section-title p { color: var(--muted); margin: 6px 0 0; }
        .eyebrow { display: inline-block; font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--primary); background: var(--primary-soft); padding: 5px 12px; border-radius: 999px; margin-bottom: 10px; }

        .page-hero { background: var(--brand-gradient); color: #fff; padding: 42px 0 36px; position: relative; overflow: hidden; }
        .page-hero::after { content: ''; position: absolute; right: -80px; top: -80px; width: 280px; height: 280px; border-radius: 50%; background: rgba(255,255,255,.12); }
        .page-hero h1 { font-weight: 800; font-size: 1.9rem; margin: 0 0 6px; position: relative; z-index: 1; }
        .page-hero .breadcrumb { margin: 0; position: relative; z-index: 1; }
        .page-hero .breadcrumb-item, .page-hero .breadcrumb-item a { color: rgba(255,255,255,.85); font-size: 14px; }
        .page-hero .breadcrumb-item.active { color: #fff; }
        .page-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.6); }

        .card-soft { background: #fff; border: 1px solid var(--line); border-radius: var(--radius); }
        .rich-content { line-height: 1.8; font-size: 15.5px; }
        .rich-content img { height: auto; border-radius: 10px; margin: 8px 0; }
        .rich-content h2, .rich-content h3 { font-weight: 700; margin: 1.4rem 0 .7rem; font-size: 1.2rem; }
        .rich-content table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .rich-content table td, .rich-content table th { border: 1px solid var(--line); padding: 8px 10px; }
        .rich-content iframe { max-width: 100%; }

        .pagination { gap: 6px; flex-wrap: wrap; }
        .pagination .page-link { border-radius: 10px !important; border: 1px solid var(--line); color: var(--ink); min-width: 40px; text-align: center; }
        .pagination .page-item.active .page-link { background: var(--primary); border-color: var(--primary); color: #fff; }

        /* ---------- Footer ---------- */
        .site-footer { background: #0b2a29; color: rgba(255,255,255,.75); padding-top: 56px; font-size: 14px; }
        .site-footer h5 { color: #fff; font-weight: 700; font-size: 16px; margin-bottom: 18px; position: relative; padding-bottom: 10px; }
        .site-footer h5::after { content: ''; position: absolute; left: 0; bottom: 0; width: 36px; height: 3px; border-radius: 3px; background: var(--brand-gradient); }
        .site-footer a { color: rgba(255,255,255,.75); }
        .site-footer a:hover { color: #fff; }
        .footer-links li { margin-bottom: 10px; }
        .footer-links a::before { content: '\f105'; font-family: 'Font Awesome 6 Free'; font-weight: 900; margin-right: 8px; font-size: 12px; color: var(--leaf); }
        .footer-legal p { margin-bottom: 8px; line-height: 1.6; }
        .footer-legal i { color: var(--leaf); width: 18px; }
        .footer-company { color: #fff; font-weight: 800; font-size: 15px; text-transform: uppercase; line-height: 1.5; }
        .footer-social a { width: 38px; height: 38px; border-radius: 50%; display: inline-grid; place-items: center; background: rgba(255,255,255,.08); margin-right: 6px; color: #fff; }
        .footer-social a:hover { background: var(--primary); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,.08); margin-top: 40px; padding: 18px 0; font-size: 13px; }
        .bct-badge img { width: 170px; height: 64px; object-fit: cover; object-position: center; }

        /* ---------- Nút liên hệ nổi ---------- */
        .float-contact { position: fixed; right: 16px; bottom: 20px; z-index: 1040; display: flex; flex-direction: column; gap: 10px; }
        .float-contact a, .float-contact button { width: 50px; height: 50px; border-radius: 50%; display: grid; place-items: center; color: #fff; font-size: 20px; box-shadow: 0 6px 18px rgba(0,0,0,.18); border: 0; }
        .float-contact .fc-phone { background: var(--danger); animation: pulse 1.8s infinite; }
        .float-contact .fc-zalo { background: #0068ff; font-weight: 800; font-size: 13px; }
        .float-contact .fc-mess { background: linear-gradient(135deg,#00b2ff,#a033ff); }
        .float-contact .fc-top { background: var(--primary-dark); opacity: 0; pointer-events: none; transition: .2s; }
        .float-contact .fc-top.show { opacity: 1; pointer-events: auto; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(226,72,61,.5); } 70% { box-shadow: 0 0 0 14px rgba(226,72,61,0); } 100% { box-shadow: 0 0 0 0 rgba(226,72,61,0); } }

        @media (max-width: 991.98px) {
            .brand-logo img { height: 40px; }
            .brand-logo .brand-name { font-size: 13px; max-width: 170px; }
            .section { padding: 40px 0; }
            .section-title h2 { font-size: 1.3rem; }
            .page-hero h1 { font-size: 1.45rem; }
        }
        @yield('extra-styles')
    </style>
    @stack('styles')
</head>
<body>

{{-- ================= TOP BAR ================= --}}
<div class="topbar d-none d-md-block">
    <div class="container d-flex justify-content-between align-items-center py-2">
        <div class="d-flex gap-4">
            @if(!empty($site['hotline']))<a href="tel:{{ $hotlineRaw }}"><i class="fas fa-phone-alt me-1"></i> {{ $site['hotline'] }}</a>@endif
            @if(!empty($site['email']))<a href="mailto:{{ $site['email'] }}"><i class="fas fa-envelope me-1"></i> {{ $site['email'] }}</a>@endif
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('frontend.support') }}"><i class="fas fa-headset me-1"></i> Hỗ trợ khách hàng</a>
            @auth
                <a href="{{ route('admin.dashboard') }}"><i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name }}</a>
            @else
                <a href="{{ route('admin.login') }}"><i class="fas fa-sign-in-alt me-1"></i> Đăng nhập</a>
                <a href="{{ route('admin.register') }}"><i class="fas fa-user-plus me-1"></i> Đăng ký</a>
            @endauth
        </div>
    </div>
</div>

{{-- ================= HEADER ================= --}}
<header class="site-header">
    <div class="container d-flex align-items-center gap-3 py-2 py-lg-3">
        <button class="btn d-lg-none p-1 fs-4" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-label="Mở menu">
            <i class="fas fa-bars"></i>
        </button>

        <a href="{{ route('frontend.home') }}" class="brand-logo me-lg-4">
            <img src="{{ $site['logo_url'] }}" alt="{{ $siteName }}">
            <span class="brand-name d-none d-sm-block">{{ $siteName }}<small>Sống khỏe – Kinh doanh bền vững</small></span>
        </a>

        <form action="{{ route('frontend.product.index') }}" method="GET" class="header-search d-none d-lg-block">
            <input type="search" name="q" class="form-control" placeholder="Tìm sản phẩm..." value="{{ request('q') }}" aria-label="Tìm sản phẩm">
            <button type="submit" aria-label="Tìm"><i class="fas fa-search"></i></button>
        </form>

        <div class="ms-auto d-flex align-items-center gap-3">
            @if(!empty($site['hotline']))
                <a href="tel:{{ $hotlineRaw }}" class="header-hotline d-none d-xl-flex">
                    <span class="icon"><i class="fas fa-phone-volume"></i></span>
                    <span><small>Hotline tư vấn</small><strong>{{ $site['hotline'] }}</strong></span>
                </a>
            @endif
            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn btn-brand btn-sm d-none d-sm-inline-block"><i class="fas fa-user me-1"></i> Tài khoản</a>
            @else
                <a href="{{ route('admin.login') }}" class="btn btn-brand btn-sm"><i class="fas fa-sign-in-alt me-1"></i> Đăng nhập</a>
            @endauth
        </div>
    </div>

    <nav class="main-nav d-none d-lg-block">
        <div class="container">
            <ul class="nav">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.home') ? 'active' : '' }}" href="{{ route('frontend.home') }}">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.about') ? 'active' : '' }}" href="{{ route('frontend.about') }}">Giới thiệu</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('frontend.product.*') ? 'active' : '' }}" href="{{ route('frontend.product.index') }}" data-bs-toggle="dropdown" role="button">Sản phẩm</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('frontend.product.index') }}"><i class="fas fa-th-large me-2 text-brand"></i>Tất cả sản phẩm</a></li>
                        @foreach($menuCategories as $cat)
                            <li><a class="dropdown-item" href="{{ route('frontend.product.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.news.*') ? 'active' : '' }}" href="{{ route('frontend.news.index') }}">Tin tức</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('frontend.support', 'frontend.policy.*') ? 'active' : '' }}" href="{{ route('frontend.support') }}" data-bs-toggle="dropdown" role="button">Hỗ trợ</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('frontend.support') }}"><i class="fas fa-headset me-2 text-brand"></i>Trung tâm hỗ trợ</a></li>
                        @foreach($footerPolicies as $policy)
                            <li><a class="dropdown-item" href="{{ route('frontend.policy.show', $policy->slug) }}">{{ $policy->title }}</a></li>
                        @endforeach
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.contact') ? 'active' : '' }}" href="{{ route('frontend.contact') }}">Liên hệ</a></li>
            </ul>
        </div>
    </nav>
</header>

{{-- Menu mobile --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
    <div class="offcanvas-header border-bottom">
        <img src="{{ $site['logo_url'] }}" alt="" style="height:40px">
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('frontend.product.index') }}" method="GET" class="header-search mb-3" style="max-width:none">
            <input type="search" name="q" class="form-control" placeholder="Tìm sản phẩm..." value="{{ request('q') }}">
            <button type="submit" aria-label="Tìm"><i class="fas fa-search"></i></button>
        </form>
        <div class="list-group list-group-flush">
            <a class="list-group-item list-group-item-action fw-semibold" href="{{ route('frontend.home') }}">Trang chủ</a>
            <a class="list-group-item list-group-item-action fw-semibold" href="{{ route('frontend.about') }}">Giới thiệu</a>
            <a class="list-group-item list-group-item-action fw-semibold" href="{{ route('frontend.product.index') }}">Sản phẩm</a>
            @foreach($menuCategories as $cat)
                <a class="list-group-item list-group-item-action ps-4 small" href="{{ route('frontend.product.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a>
            @endforeach
            <a class="list-group-item list-group-item-action fw-semibold" href="{{ route('frontend.news.index') }}">Tin tức</a>
            <a class="list-group-item list-group-item-action fw-semibold" href="{{ route('frontend.support') }}">Hỗ trợ</a>
            <a class="list-group-item list-group-item-action fw-semibold" href="{{ route('frontend.contact') }}">Liên hệ</a>
        </div>
        <div class="mt-4 d-grid gap-2">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn btn-brand">Tài khoản của tôi</a>
            @else
                <a href="{{ route('admin.login') }}" class="btn btn-brand">Đăng nhập</a>
                <a href="{{ route('admin.register') }}" class="btn btn-outline-brand">Đăng ký</a>
            @endauth
        </div>
    </div>
</div>

<main>
    @yield('content')
</main>

{{-- ================= FOOTER ================= --}}
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            {{-- Thông tin doanh nghiệp (theo yêu cầu website TMĐT bán hàng - Bộ Công Thương) --}}
            <div class="col-lg-5">
                <a href="{{ route('frontend.home') }}" class="d-inline-flex align-items-center gap-2 mb-3">
                    <img src="{{ $site['logo_url'] }}" alt="{{ $siteName }}" style="height:54px;background:#fff;border-radius:12px;padding:4px">
                </a>
                <div class="footer-company mb-3">{{ $siteName }}</div>
                <div class="footer-legal">
                    @if(!empty($site['ma_so_doanh_nghiep']))
                        <p><i class="fas fa-id-card"></i> Mã số doanh nghiệp: <strong class="text-white">{{ $site['ma_so_doanh_nghiep'] }}</strong></p>
                    @endif
                    @if(!empty($site['GCNDKDN']))
                        <p><i class="fas fa-file-signature"></i> {{ $site['GCNDKDN'] }}</p>
                    @endif
                    @if(!empty($site['nguoi_dai_dien']))
                        <p><i class="fas fa-user-tie"></i> {{ $site['nguoi_dai_dien'] }}</p>
                    @endif
                    @if(!empty($site['address']))
                        <p><i class="fas fa-map-marker-alt"></i> Địa chỉ: {{ $site['address'] }}</p>
                    @endif
                    @if(!empty($site['hotline']))
                        <p><i class="fas fa-phone-alt"></i> Điện thoại: <a href="tel:{{ $hotlineRaw }}">{{ $site['hotline'] }}</a></p>
                    @endif
                    @if(!empty($site['email']))
                        <p><i class="fas fa-envelope"></i> Email: <a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></p>
                    @endif
                    @if(!empty($site['don_vi_phan_phoi']))
                        <p><i class="fas fa-truck"></i> {{ $site['don_vi_phan_phoi'] }}</p>
                    @endif
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <h5>Về chúng tôi</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('frontend.about') }}">Giới thiệu</a></li>
                    <li><a href="{{ route('frontend.product.index') }}">Sản phẩm</a></li>
                    <li><a href="{{ route('frontend.news.index') }}">Tin tức</a></li>
                    <li><a href="{{ route('frontend.contact') }}">Liên hệ</a></li>
                    <li><a href="{{ route('admin.register') }}">Đăng ký thành viên</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-3">
                <h5>Chính sách &amp; hỗ trợ</h5>
                <ul class="list-unstyled footer-links">
                    @forelse($footerPolicies as $policy)
                        <li><a href="{{ route('frontend.policy.show', $policy->slug) }}">{{ $policy->title }}</a></li>
                    @empty
                        <li><a href="{{ route('frontend.support') }}">Trung tâm hỗ trợ</a></li>
                    @endforelse
                </ul>
            </div>

            <div class="col-lg-2">
                <h5>Kết nối</h5>
                <div class="footer-social mb-3">
                    @if(!empty($site['facebook']))<a href="{{ $site['facebook'] }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>@endif
                    @if(!empty($site['zalo']))<a href="{{ $site['zalo'] }}" target="_blank" rel="noopener" aria-label="Zalo" style="font-weight:800;font-size:11px">Zalo</a>@endif
                    @if(!empty($site['youtube']))<a href="{{ $site['youtube'] }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>@endif
                    @if(!empty($site['instagram']))<a href="{{ $site['instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>@endif
                    @if(!empty($site['tiktok']))<a href="{{ $site['tiktok'] }}" target="_blank" rel="noopener" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>@endif
                </div>
                {{-- Chỉ hiện logo khi đã có link hồ sơ trên online.gov.vn (Cài đặt > link_bo_cong_thuong) --}}
                @if(!empty($site['link_bo_cong_thuong']))
                    <a href="{{ $site['link_bo_cong_thuong'] }}" target="_blank" rel="noopener nofollow" class="bct-badge d-inline-block" title="Đã thông báo Bộ Công Thương">
                        <img src="{{ asset('bo-cong-thuong.png') }}" alt="Đã thông báo Bộ Công Thương">
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>© {{ date('Y') }} {{ $siteName }}. Bảo lưu mọi quyền.</span>
            <span>
                <a href="{{ route('frontend.policy.show', 'chinh-sach-bao-mat-thong-tin') }}">Bảo mật</a> ·
                <a href="{{ route('frontend.policy.show', 'dieu-khoan-su-dung') }}">Điều khoản</a> ·
                <a href="{{ route('frontend.support') }}">Hỗ trợ</a>
            </span>
        </div>
    </div>
</footer>

{{-- Nút liên hệ nổi --}}
<div class="float-contact">
    @if(!empty($site['zalo']))<a href="{{ $site['zalo'] }}" target="_blank" rel="noopener" class="fc-zalo" aria-label="Chat Zalo">Zalo</a>@endif
    @if(!empty($site['messenger']))<a href="{{ $site['messenger'] }}" target="_blank" rel="noopener" class="fc-mess" aria-label="Messenger"><i class="fab fa-facebook-messenger"></i></a>@endif
    @if($hotlineRaw)<a href="tel:{{ $hotlineRaw }}" class="fc-phone" aria-label="Gọi hotline"><i class="fas fa-phone-alt"></i></a>@endif
    <button type="button" class="fc-top" id="backToTop" aria-label="Lên đầu trang"><i class="fas fa-arrow-up"></i></button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        var btn = document.getElementById('backToTop');
        window.addEventListener('scroll', function () { btn.classList.toggle('show', window.scrollY > 500); });
        btn.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });

        // Desktop: rê chuột mở dropdown menu, bấm vào tiêu đề thì đi tới trang
        document.querySelectorAll('.main-nav .dropdown').forEach(function (dd) {
            var toggle = dd.querySelector('.dropdown-toggle');
            toggle.addEventListener('click', function (e) { if (window.innerWidth >= 992) { window.location = toggle.getAttribute('href'); } });
            dd.addEventListener('mouseenter', function () { bootstrap.Dropdown.getOrCreateInstance(toggle).show(); });
            dd.addEventListener('mouseleave', function () { bootstrap.Dropdown.getOrCreateInstance(toggle).hide(); });
        });
    })();
</script>
@stack('scripts')
</body>
</html>
