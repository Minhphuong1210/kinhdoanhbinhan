<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <meta name="description" content="@yield('meta_description', '')">

    {{-- Bootstrap 5 CSS --}}
    <link  rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #1a6b3c;
            --primary-dark: #134f2d;
            --primary-light: #e8f5ee;
            --accent: #f5a623;
            --text-dark: #1a1a2e;
            --text-muted: #6c757d;
            --border-light: #e9ecef;
        }

        * { font-family: 'Inter', sans-serif; }

        /* ===== TOPBAR ===== */
        .topbar {
            background: var(--primary-dark);
            color: rgba(255,255,255,.75);
            font-size: 0.78rem;
            padding: 5px 0;
        }
        .topbar a { color: rgba(255,255,255,.75); text-decoration: none; }
        .topbar a:hover { color: #fff; }

        /* ===== HEADER ===== */
        .site-header {
            background: #fff;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .site-header .navbar-brand img {
            height: 52px;
            object-fit: contain;
        }
        .site-header .search-box {
            position: relative;
            flex: 1;
            max-width: 460px;
        }
        .site-header .search-box input {
            border-radius: 50px;
            border: 2px solid var(--border-light);
            padding: 0.45rem 3rem 0.45rem 1.1rem;
            font-size: .9rem;
            transition: border-color .2s;
        }
        .site-header .search-box input:focus {
            border-color: var(--primary);
            box-shadow: none;
            outline: none;
        }
        .site-header .search-box .btn-search {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary);
            border: none;
            border-radius: 50%;
            width: 32px; height: 32px;
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem;
        }
        .cart-btn {
            position: relative;
            color: var(--text-dark);
            font-size: 1.4rem;
            text-decoration: none;
        }
        .cart-btn .badge-cart {
            position: absolute;
            top: -6px; right: -8px;
            background: var(--accent);
            color: #fff;
            font-size: .65rem;
            width: 18px; height: 18px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
        }
        .user-info-btn {
            display: flex; align-items: center; gap: 6px;
            font-size: .88rem; color: var(--text-dark); text-decoration: none;
        }
        .user-info-btn .avatar {
            width: 34px; height: 34px;
            background: var(--primary);
            border-radius: 50%;
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .85rem;
        }

        /* ===== NAVIGATION ===== */
        .main-nav {
            background: var(--primary);
        }
        .main-nav .nav-link {
            color: rgba(255,255,255,.9) !important;
            font-weight: 500;
            font-size: .9rem;
            padding: .65rem 1.1rem !important;
            position: relative;
            transition: color .2s;
        }
        .main-nav .nav-link:hover,
        .main-nav .nav-link.active {
            color: #fff !important;
        }
        .main-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0; left: 50%; right: 50%;
            height: 3px;
            background: var(--accent);
            transition: left .25s, right .25s;
            border-radius: 2px 2px 0 0;
        }
        .main-nav .nav-link:hover::after,
        .main-nav .nav-link.active::after {
            left: 0; right: 0;
        }
        .main-nav .dropdown-menu {
            border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,.15);
            border-radius: 8px;
            padding: .5rem 0;
        }
        .main-nav .dropdown-item {
            font-size: .88rem;
            padding: .5rem 1.2rem;
        }
        .main-nav .dropdown-item:hover {
            background: var(--primary-light);
            color: var(--primary);
        }

        /* ===== MARQUEE ===== */
        .marquee-bar {
            background: var(--primary);
            color: #fff;
            padding: 7px 0;
            font-size: .84rem;
            font-weight: 500;
            overflow: hidden;
        }
        .marquee-inner {
            display: flex;
            white-space: nowrap;
            animation: marqueeScroll 40s linear infinite;
        }
        .marquee-inner:hover { animation-play-state: paused; }
        .marquee-item { padding: 0 40px; }
        .marquee-item i { margin-right: 5px; }
        @keyframes marqueeScroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* ===== FOOTER ===== */
        .site-footer {
            background: var(--primary-dark);
            color: rgba(255,255,255,.75);
            padding: 48px 0 20px;
        }
        .site-footer h6 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: .95rem;
        }
        .site-footer a {
            color: rgba(255,255,255,.65);
            text-decoration: none;
            display: block;
            margin-bottom: .35rem;
            font-size: .875rem;
            transition: color .2s;
        }
        .site-footer a:hover { color: var(--accent); }
        .site-footer .footer-bottom {
            border-top: 1px solid rgba(255,255,255,.1);
            padding-top: 16px;
            margin-top: 32px;
            font-size: .8rem;
            color: rgba(255,255,255,.5);
        }
        .social-btn {
            width: 36px; height: 36px;
            background: rgba(255,255,255,.1);
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff;
            text-decoration: none;
            margin-right: 6px;
            transition: background .2s;
            font-size: .9rem;
        }
        .social-btn:hover { background: var(--accent); color: #fff; }

        /* ===== MOBILE SCROLL HEADER ===== */
        @media (max-width: 768px) {
            /* Header wrapper chứa logo+search */
            .site-header {
                transition: transform .3s ease, box-shadow .3s;
            }
            .site-header.header-hidden {
                /* Ẩn logo, chỉ giữ lại search bằng cách thu nhỏ */
            }
            /* Logo ẩn khi scroll */
            #header-logo {
                transition: all .3s ease;
                overflow: hidden;
                max-width: 200px;
            }
            .header-scrolled #header-logo {
                max-width: 0;
                opacity: 0;
                margin: 0 !important;
                padding: 0 !important;
            }
            /* User/Cart ẩn khi scroll */
            .header-scrolled #header-actions {
                display: none !important;
            }
            /* Nav ẩn khi scroll xuống */
            .main-nav {
                transition: transform .3s ease, opacity .3s;
            }
            .nav-scrolled {
                transform: translateY(-100%);
                opacity: 0;
                pointer-events: none;
                height: 0;
                overflow: hidden;
            }
        }

        @yield('extra-styles')
    </style>

    @php
        // Load settings theo vi_tri_web (dùng chung cho topbar, header logo, footer)
        $siteSettings = \App\Models\Setting::forWeb()->pluck('value', 'name')->toArray();
        $siteLogo     = $siteSettings['logo'] ?? null;
        $siteHotline  = $siteSettings['hotline'] ?? '';
        $siteEmail    = $siteSettings['email'] ?? '';
        $siteFacebook = $siteSettings['facebook'] ?? '#';
    @endphp

    @stack('head-scripts')
</head>
<body>

{{-- TOPBAR --}}
<div class="topbar d-none d-md-block">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-telephone-fill me-1"></i>
                <a href="tel:{{ $siteHotline }}">Hotline: {{ $siteHotline ?: '1800 xxxx' }}</a>
                <span class="mx-2">|</span>
                <i class="bi bi-envelope-fill me-1"></i>
                <a href="mailto:{{ $siteEmail }}">{{ $siteEmail ?: 'info@congty.vn' }}</a>
            </div>
            <div>
                <a href="{{ $siteFacebook }}"><i class="bi bi-facebook me-1"></i>Facebook</a>
                <span class="mx-2">|</span>
                @auth
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-grid-fill me-1"></i>Quản lý
                    </a>
                @else
                    <a href="{{ route('admin.login') }}">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

{{-- HEADER --}}
<header class="site-header">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-center gap-3">
            {{-- Logo --}}
            <a class="navbar-brand flex-shrink-0 me-2" id="header-logo" href="{{ route('frontend.home') }}">
                @if($siteLogo)
                    <img src="{{ cdn($siteLogo) }}" alt="{{ config('app.name') }}">
                @else
                    <span style="font-weight:800; color:var(--primary); font-size:1.3rem;">🌿 {{ config('app.name') }}</span>
                @endif
            </a>

            {{-- Search (center) --}}
            <form class="search-box flex-grow-1" action="#" method="get">
                <input type="text" name="q" class="form-control" placeholder="Tìm kiếm sản phẩm...">
                <button type="submit" class="btn-search"  aria-label="Tìm kiếm">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            {{-- User + Cart (right) --}}
            <div class="d-flex align-items-center gap-3 flex-shrink-0" id="header-actions">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="user-info-btn">
                        <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="d-none d-lg-block">{{ auth()->user()->name }}</span>
                    </a>
                @else
                    <a href="{{ route('admin.login') }}" class="user-info-btn">
                        <span class="avatar"><i class="bi bi-person"></i></span>
                        <span class="d-none d-lg-block">Đăng nhập</span>
                    </a>
                @endauth

                <a href="#" class="cart-btn" title="Giỏ hàng">
                    <i class="bi bi-bag"></i>
                    <span class="badge-cart">0</span>
                </a>
            </div>
        </div>
    </div>
</header>

{{-- MAIN NAVIGATION --}}
<nav class="main-nav">
    <div class="container">
        <div class="d-flex align-items-center">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.home') ? 'active' : '' }}"
                       href="{{ route('frontend.home') }}">
                        <i class="bi bi-house-fill me-1 d-none d-xl-inline"></i>Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('frontend.product.*') ? 'active' : '' }}"
                       href="{{ route('frontend.product.index') }}">
                        <i class="bi bi-grid-fill me-1 d-none d-xl-inline"></i>Sản phẩm
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('frontend.news.*') ? 'active' : '' }}"
                       href="{{ route('frontend.news.index') }}" data-bs-toggle="dropdown">
                        <i class="bi bi-newspaper me-1 d-none d-xl-inline"></i>Tin tức
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('frontend.news.index') }}">Tất cả tin tức</a></li>
                        @isset($navPostCategories)
                            @foreach($navPostCategories as $cat)
                                <li>
                                    <a class="dropdown-item" href="{{ route('frontend.news.index', ['slug' => $cat->slug]) }}">
                                        <i class="bi bi-chevron-right me-1 text-muted" style="font-size:.75rem"></i>
                                        {{ $cat->name }}
                                    </a>
                                </li>
                            @endforeach
                        @endisset
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>

{{-- MARQUEE BAR --}}
@hasSection('no-marquee')
@else
    @isset($latestOrders)
        @if($latestOrders->count())
            <div class="marquee-bar">
                <div class="container-fluid px-0">
                    <div class="marquee-inner" id="marqueeEl">
                        @php $items = $latestOrders->toArray(); @endphp
                        @foreach(array_merge($items, $items) as $order)
                            <span class="marquee-item">
                                <i class="bi bi-bag-check-fill"></i>
                                <strong>{{ $order['user']['name'] ?? 'Khách hàng' }}</strong>
                                vừa mua đơn hàng trị giá
                                <strong>{{ number_format($order['total_price'], 0, ',', '.') }}₫</strong>
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endisset
@endif

{{-- MAIN CONTENT --}}
<main>
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    @yield('content')
</main>

@php
    $_fSettings   = \App\Models\Setting::forWeb()->pluck('value', 'name')->toArray();
    $_fChinhSachs = \App\Models\ChinhSach::forWeb()->where('status', 1)->get();
@endphp

<style>
    /* ═══════════════════════ COOKIE BANNER ═══════════════════════ */
    .cookie-banner{position:fixed;bottom:0;left:0;right:0;z-index:9999;background:#fff;box-shadow:0 -4px 20px rgba(0,0,0,.12);padding:14px 20px;display:flex;justify-content:center;align-items:center;}
    .cookie-banner__frame{max-width:960px;width:100%;display:flex;gap:16px;align-items:center;flex-wrap:wrap;}
    .cookie-banner__text{margin:0;font-size:13px;color:#444;line-height:1.6;flex:1;min-width:260px;}
    .cookie-banner__controls{display:flex;gap:8px;flex-shrink:0;}
    .cookie-btn{padding:7px 18px;border-radius:50px;font-size:13px;font-weight:600;cursor:pointer;border:none;transition:.2s;}
    .cookie-btn.primary{background:var(--primary);color:#fff;}
    .cookie-btn.primary:hover{background:var(--primary-dark);}
    .cookie-btn.secondary{background:#f1f1f1;color:#555;border:1px solid #ddd;}
    .cookie-btn.secondary:hover{background:#e2e2e2;}

    /* ═══════════════════════ FOOTER MAIN ════════════════════════= */
    .htx-footer{background:linear-gradient(160deg,#1a3d28 0%,#1e5c38 40%,#2d7a4f 100%);color:rgba(255,255,255,.88);position:relative;overflow:hidden;}
    .htx-footer::before{content:'';position:absolute;top:-80px;right:-80px;width:320px;height:320px;border-radius:50%;background:rgba(255,255,255,.03);pointer-events:none;}
    .htx-footer::after{content:'';position:absolute;bottom:-60px;left:-60px;width:240px;height:240px;border-radius:50%;background:rgba(255,255,255,.03);pointer-events:none;}

    .htx-footer-top{padding:52px 0 40px;position:relative;z-index:1;}
    .htx-footer-brand .brand-name{font-size:1.35rem;font-weight:800;color:#fff;letter-spacing:-.3px;}
    .htx-footer-brand .brand-sub{font-size:.75rem;color:rgba(255,255,255,.55);margin-top:2px;}
    .htx-footer-brand .brand-desc{font-size:.85rem;line-height:1.8;color:rgba(255,255,255,.7);margin-top:14px;}
    .htx-footer-brand .social-links{display:flex;gap:10px;margin-top:18px;}
    .htx-footer-brand .social-links a{width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.9rem;transition:.2s;text-decoration:none;}
    .htx-footer-brand .social-links a:hover{background:rgba(255,255,255,.25);transform:scale(1.1);}

    .htx-footer-col h5{font-size:.8rem;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.8px;margin-bottom:18px;padding-bottom:10px;border-bottom:2px solid rgba(255,255,255,.12);position:relative;}
    .htx-footer-col h5::after{content:'';position:absolute;bottom:-2px;left:0;width:32px;height:2px;background:var(--accent);}
    .htx-footer-col ul{list-style:none;padding:0;margin:0;}
    .htx-footer-col ul li{margin-bottom:9px;}
    .htx-footer-col ul li a,.htx-footer-col ul li span{font-size:.84rem;color:rgba(255,255,255,.72);text-decoration:none;transition:.2s;display:flex;align-items:flex-start;gap:8px;line-height:1.5;}
    .htx-footer-col ul li a:hover{color:#fff;padding-left:4px;}
    .htx-footer-col ul li i{color:var(--accent);margin-top:2px;flex-shrink:0;width:14px;}

    .footer-map{border-radius:12px;overflow:hidden;border:2px solid rgba(255,255,255,.12);box-shadow:0 8px 24px rgba(0,0,0,.25);}
    .footer-map iframe{display:block;width:100%;height:180px;border:none;}

    .htx-footer-divider{border:none;border-top:1px solid rgba(255,255,255,.1);margin:0;}
    .htx-footer-bottom{background:rgba(0,0,0,.2);padding:16px 0;position:relative;z-index:1;}
    .htx-footer-bottom .bottom-left{font-size:.8rem;color:rgba(255,255,255,.55);}
    .htx-footer-bottom .bottom-left strong{color:rgba(255,255,255,.85);}
    .htx-footer-bottom .bottom-right img{height:36px;opacity:.85;transition:.2s;}
    .htx-footer-bottom .bottom-right img:hover{opacity:1;}

    /* Floating social */
    .fixed-social{position:fixed;right:16px;bottom:90px;display:flex;flex-direction:column;gap:8px;z-index:1000;}
    .fixed-social a{width:44px;height:44px;border-radius:50%;background:#fff;box-shadow:0 4px 14px rgba(0,0,0,.15);display:flex;align-items:center;justify-content:center;transition:.25s;text-decoration:none;}
    .fixed-social a:hover{transform:scale(1.12);box-shadow:0 6px 20px rgba(0,0,0,.2);}
    .fixed-social a img{width:24px;height:24px;object-fit:contain;}
    .fixed-social .back-top{background:var(--primary);}
    .fixed-social .back-top i{color:#fff;font-size:1rem;}

    @media(max-width:768px){.htx-footer-top{padding:36px 0 28px;}.cookie-banner__frame{flex-direction:column;align-items:flex-start;}}
</style>

{{-- Cookie Banner --}}
<div class="cookie-banner" id="cookieBanner">
    <div class="cookie-banner__frame">
        <p class="cookie-banner__text">
            <i class="bi bi-info-circle-fill me-2" style="color:var(--primary)"></i>
            Các thông tin trên website này chỉ mang tính chất trợ giúp tìm hiểu về sản phẩm, không nhằm mục đích quảng cáo.
            Vui lòng xác nhận Quý khách là nhân viên y tế hoặc có nhu cầu tìm hiểu về sản phẩm.
        </p>
        <div class="cookie-banner__controls">
            <button class="cookie-btn primary" onclick="closeCookie()">Chấp nhận</button>
            <button class="cookie-btn secondary" onclick="closeCookie()">Từ chối</button>
        </div>
    </div>
</div>
<script>
    function closeCookie(){
        document.getElementById('cookieBanner').style.display='none';
        document.cookie="cookie_ok=1;path=/;max-age="+(60*60*24*30);
    }
    window.addEventListener('DOMContentLoaded',function(){
        if(document.cookie.includes('cookie_ok=1'))
            document.getElementById('cookieBanner').style.display='none';
    });
</script>

{{-- Footer --}}
<footer class="htx-footer mt-0">
    <div class="htx-footer-top">
        <div class="container">
            <div class="row g-5">

                {{-- Cột 1: Thương hiệu + social --}}
                <div class="col-lg-4">
                    <div class="htx-footer-brand">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div style="width:40px;height:40px;background:rgba(255,255,255,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-leaf text-white"></i>
                            </div>
                            <div>
                                <div class="brand-name">{{ $_fSettings['name'] ?? config('app.name') }}</div>
                                <div class="brand-sub">Nông sản sạch — Giá trị bền vững</div>
                            </div>
                        </div>
                        <p class="brand-desc">🌿 Chúng tôi cam kết mang đến những sản phẩm chất lượng nhất từ thiên nhiên, vì sức khỏe cộng đồng và sự phát triển bền vững.</p>
                        <div class="social-links">
                            @if(!empty($_fSettings['facebook']))
                                <a href="{{ $_fSettings['facebook'] }}" target="_blank" title="Facebook"><i class="bi bi-facebook"></i></a>
                            @endif
                            @if(!empty($_fSettings['zalo']))
                                <a href="{{ $_fSettings['zalo'] }}" target="_blank" title="Zalo" style="font-size:.65rem;font-weight:800;">Zalo</a>
                            @endif
                            @if(!empty($_fSettings['messenger']))
                                <a href="{{ $_fSettings['messenger'] }}" target="_blank" title="Messenger"><i class="bi bi-messenger"></i></a>
                            @endif
                            @if(!empty($_fSettings['hotline']))
                                <a href="tel:{{ $_fSettings['hotline'] }}" title="Gọi ngay"><i class="bi bi-telephone-fill"></i></a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Cột 2: Thông tin liên hệ --}}
                <div class="col-lg-3 htx-footer-col">
                    <h5>Thông tin liên hệ</h5>
                    <ul>
                        @if(!empty($_fSettings['address']))
                            <li><span><i class="bi bi-geo-alt-fill"></i>{{ $_fSettings['address'] }}</span></li>
                        @endif
                        @if(!empty($_fSettings['email']))
                            <li><a href="mailto:{{ $_fSettings['email'] }}"><i class="bi bi-envelope-fill"></i>{{ $_fSettings['email'] }}</a></li>
                        @endif
                        @if(!empty($_fSettings['hotline']))
                            <li><a href="tel:{{ $_fSettings['hotline'] }}"><i class="bi bi-telephone-fill"></i>{{ $_fSettings['hotline'] }}</a></li>
                        @endif
                        @if(!empty($_fSettings['ma_so_doanh_nghiep']))
                            <li><span><i class="bi bi-card-text"></i>MST: {{ $_fSettings['ma_so_doanh_nghiep'] }}</span></li>
                        @endif
                        {{--                        @if(!empty($_fSettings['nguoi_dai_dien']))--}}
                        {{--                            <li><span><i class="bi bi-person-badge-fill"></i>Đại diện: {{ $_fSettings['nguoi_dai_dien'] }}</span></li>--}}
                        {{--                        @endif--}}
                        {{--                        @if(!empty($_fSettings['don_vi_phan_phoi']))--}}
                        {{--                            <li><span><i class="bi bi-building"></i>{{ $_fSettings['don_vi_phan_phoi'] }}</span></li>--}}
                        {{--                        @endif--}}
                    </ul>
                </div>

                {{-- Cột 3: Liên kết nhanh + Chính sách --}}
                <div class="col-lg-2 htx-footer-col">
                    <h5>Liên kết nhanh</h5>
                    <ul>
                        <li><a href="{{ route('frontend.home') }}"><i class="bi bi-chevron-right"></i>Trang chủ</a></li>
                        <li><a href="{{ route('frontend.product.index') }}"><i class="bi bi-chevron-right"></i>Sản phẩm</a></li>
                        <li><a href="{{ route('frontend.news.index') }}"><i class="bi bi-chevron-right"></i>Tin tức</a></li>
                        @foreach($_fChinhSachs as $_fCs)
                            <li><a href="{{ url('chinh-sach'.$_fCs->url) }}"><i class="bi bi-chevron-right"></i>{{ $_fCs->title }}</a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Cột 4: Bản đồ --}}
                <div class="col-lg-3 htx-footer-col">
                    <h5><i class="bi bi-map-fill me-1" style="color:var(--accent)"></i>Vị trí của chúng tôi</h5>
                    <div class="footer-map mb-3">
                        @if(!empty($_fSettings['google_map']))
                            {!! $_fSettings['google_map'] !!}
                        @else
                            <iframe  title="Bản đồ vị trí công ty" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4!2d106.698!3d10.7769!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTDCsDQ2JzM3LjAiTiAxMDbCsDQxJzUyLjgiRQ!5e0!3m2!1svi!2svn!4v1" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        @endif
                    </div>
                    @if(!empty($_fSettings['address']))
                        <div style="font-size:.78rem;color:rgba(255,255,255,.6);display:flex;gap:6px;align-items:flex-start;">
                            <i class="bi bi-pin-map-fill" style="color:var(--accent);margin-top:2px;"></i>
                            <span>{{ $_fSettings['address'] }}</span>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <hr class="htx-footer-divider">

    <style>
        .bo_cong_thuong a img {
            width: 200px;
        }
    </style>


    <div class="htx-footer-bottom">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="col-md-6">
                    © {{ date('Y') }} Bản quyền thuộc về <strong>{{ $_fSettings['name'] ?? config('app.name') }}</strong>. Bảo lưu mọi quyền.
                </div>
                <div class="col-md-4 bo_cong_thuong">
                    <a href="http://online.gov.vn/nen-tang/a979dd53-3dac-4d77-8d19-343064252b06" target="_blank" title="Đã thông báo Bộ Công Thương">
                        <img src="{{ cdn('bo-cong-thuong.png') }}" alt="BCT">
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

{{-- Floating Social --}}
<div class="fixed-social">
    <a href="#" class="back-top" id="backToTop" title="Lên đầu trang">
        <i class="bi bi-chevron-up"></i>
    </a>
    @if(!empty($_fSettings['facebook']))
        <a href="{{ $_fSettings['facebook'] }}" target="_blank" title="Facebook" style="background:#1877f2;color:#fff;">
            <i class="bi bi-facebook"></i>
        </a>
    @endif
    @if(!empty($_fSettings['zalo']))
        <a href="{{ $_fSettings['zalo'] }}" target="_blank" title="Zalo" style="font-size:.58rem;font-weight:800;color:#0068ff;">Zalo</a>
    @endif
    @if(!empty($_fSettings['hotline']))
        <a href="tel:{{ $_fSettings['hotline'] }}" title="Gọi ngay" style="background:#22c55e;color:#fff;">
            <i class="bi bi-telephone-fill"></i>
        </a>
    @endif
</div>
<script>
    document.getElementById('backToTop').addEventListener('click',function(e){
        e.preventDefault();
        window.scrollTo({top:0,behavior:'smooth'});
    });
    window.addEventListener('scroll',function(){
        document.getElementById('backToTop').style.opacity=window.scrollY>300?'1':'0.4';
    });
</script>










{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

{{-- ══ Mobile Bottom Nav Bar ══ --}}
<style>
    .mobile-bottom-nav {
        display: none;
    }
    @media (max-width: 768px) {
        body { padding-bottom: 66px; }
        .mobile-bottom-nav {
            display: flex;
            position: fixed;
            bottom: 0; left: 0; right: 0;
            z-index: 1050;
            background: #fff;
            border-top: 1px solid #e8f0eb;
            box-shadow: 0 -4px 20px rgba(0,0,0,.10);
            height: 60px;
            align-items: stretch;
        }
        .mobile-bottom-nav .mnav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            text-decoration: none;
            color: #888;
            font-size: .62rem;
            font-weight: 600;
            padding: 8px 4px;
            border: none;
            background: none;
            transition: color .2s, background .2s;
            position: relative;
            cursor: pointer;
        }
        .mobile-bottom-nav .mnav-item i {
            font-size: 1.25rem;
            line-height: 1;
        }
        .mobile-bottom-nav .mnav-item.active,
        .mobile-bottom-nav .mnav-item:hover {
            color: var(--primary);
        }
        .mobile-bottom-nav .mnav-item.active::before {
            content: '';
            position: absolute;
            top: 0; left: 20%; right: 20%;
            height: 2.5px;
            background: var(--primary);
            border-radius: 0 0 4px 4px;
        }
        /* Nút đăng nhập nổi bật hơn */
        .mobile-bottom-nav .mnav-login {
            color: #fff;
            background: var(--primary);
            border-radius: 12px;
            margin: 8px 4px;
            flex: 1.1;
        }
        .mobile-bottom-nav .mnav-login:hover {
            background: var(--primary-dark);
            color: #fff;
        }
    }
</style>

<nav class="mobile-bottom-nav" aria-label="Mobile navigation">
    <a href="{{ route('frontend.home') }}"
       class="mnav-item {{ request()->routeIs('frontend.home') ? 'active' : '' }}">
        <i class="bi bi-house-fill"></i>
        <span>Trang chủ</span>
    </a>
    <a href="{{ route('frontend.product.index') }}"
       class="mnav-item {{ request()->routeIs('frontend.product.*') ? 'active' : '' }}">
        <i class="bi bi-grid-fill"></i>
        <span>Sản phẩm</span>
    </a>
    <a href="{{ route('frontend.news.index') }}"
       class="mnav-item {{ request()->routeIs('frontend.news.*') ? 'active' : '' }}">
        <i class="bi bi-newspaper"></i>
        <span>Tin tức</span>
    </a>
    @auth
        <a href="{{ route('admin.dashboard') }}" class="mnav-item">
            <i class="bi bi-person-circle"></i>
            <span>Tài khoản</span>
        </a>
    @else
        <a href="{{ route('admin.login') }}" class="mnav-item mnav-login">
            <i class="bi bi-box-arrow-in-right"></i>
            <span>Đăng nhập</span>
        </a>
    @endauth
</nav>

<script>
    /* ── Mobile Scroll Header Hide ── */
    (function(){
        var header  = document.querySelector('.site-header');
        var mainNav = document.querySelector('.main-nav');
        var lastY   = 0;
        var THRESHOLD = 60; // px cuộn xuống mới kích hoạt

        window.addEventListener('scroll', function(){
            if (window.innerWidth > 768) return; // chỉ mobile
            var y = window.scrollY;
            if (y > lastY && y > THRESHOLD) {
                // Cuộn xuống → ẩn logo+actions, ẩn nav
                header  && header.classList.add('header-scrolled');
                mainNav && mainNav.classList.add('nav-scrolled');
            } else {
                // Cuộn lên → hiện lại
                header  && header.classList.remove('header-scrolled');
                mainNav && mainNav.classList.remove('nav-scrolled');
            }
            lastY = y;
        }, {passive: true});
    })();
</script>

</body>

</html>
