<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bình An – Hệ sinh thái tiêu dùng</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    /* ===== RESET & BASE ===== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: #eef2f7;
      color: #0b1e18;
      overflow-x: hidden;
    }

    .container {
      max-width: 1240px;
      margin: 0 auto;
      padding: 0 28px;
    }

    /* ===== ANIMATION KEYFRAMES ===== */
    @keyframes float3d {
      0% { transform: translateY(0px) rotateX(0deg) rotateY(2deg); }
      50% { transform: translateY(-18px) rotateX(2deg) rotateY(-2deg); }
      100% { transform: translateY(0px) rotateX(0deg) rotateY(2deg); }
    }

    @keyframes pulseGlow {
      0% { box-shadow: 0 0 0 0 rgba(11, 59, 44, 0.3); }
      70% { box-shadow: 0 0 0 18px rgba(11, 59, 44, 0); }
      100% { box-shadow: 0 0 0 0 rgba(11, 59, 44, 0); }
    }

    @keyframes slideInLeft {
      0% { opacity: 0; transform: translateX(-60px) skewX(-3deg); }
      100% { opacity: 1; transform: translateX(0) skewX(0); }
    }

    @keyframes slideInRight {
      0% { opacity: 0; transform: translateX(60px) skewX(3deg); }
      100% { opacity: 1; transform: translateX(0) skewX(0); }
    }

    @keyframes scaleIn {
      0% { opacity: 0; transform: scale(0.85) rotate(-2deg); }
      100% { opacity: 1; transform: scale(1) rotate(0); }
    }

    @keyframes tickerScroll {
      0% { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }

    @keyframes shimmer {
      0% { background-position: -200% center; }
      100% { background-position: 200% center; }
    }

    /* ===== NOTIFICATION TICKER (nâng cấp) ===== */
    .top-ticker {
      background: linear-gradient(135deg, #0a2f24, #134b39);
      padding: 14px 0;
      border-bottom: 3px solid #facc15;
      box-shadow: 0 6px 24px rgba(0, 0, 0, 0.2);
      position: relative;
      z-index: 10;
      overflow: hidden;
    }

    .top-ticker::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(90deg, transparent, rgba(255, 215, 0, 0.08), transparent);
      animation: shimmer 6s infinite linear;
      background-size: 200% 100%;
      pointer-events: none;
    }

    .ticker-track {
      display: flex;
      gap: 50px;
      align-items: center;
      animation: tickerScroll 50s linear infinite;
      width: max-content;
    }

    .ticker-item {
      display: flex;
      align-items: center;
      gap: 12px;
      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(6px);
      padding: 6px 22px 6px 18px;
      border-radius: 60px;
      border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .ticker-item i {
      color: #facc15;
      font-size: 1rem;
      filter: drop-shadow(0 0 6px rgba(250, 204, 21, 0.3));
    }

    .ticker-item strong {
      color: #fff;
      font-weight: 700;
      font-size: 0.95rem;
    }

    .ticker-item .amount {
      background: #facc15;
      color: #0b2a20;
      font-weight: 800;
      padding: 2px 16px;
      border-radius: 40px;
      font-size: 0.85rem;
      box-shadow: 0 0 16px rgba(250, 204, 21, 0.25);
    }

    /* ===== HEADER ===== */
    header {
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.6);
      box-shadow: 0 12px 40px -12px rgba(0, 0, 0, 0.08);
      position: sticky;
      top: 0;
      z-index: 99;
      padding: 10px 0;
    }

    .header-grid {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .logo-block {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .logo-icon {
      background: linear-gradient(145deg, #0b3b2c, #1d6b52);
      width: 48px;
      height: 48px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #facc15;
      font-size: 1.6rem;
      box-shadow: 0 8px 20px rgba(11, 59, 44, 0.25);
      transform: rotate(-4deg);
      transition: 0.3s;
    }

    .logo-block:hover .logo-icon {
      transform: rotate(0deg) scale(1.05);
    }

    .logo-text h1 {
      font-size: 1.7rem;
      font-weight: 800;
      letter-spacing: -0.5px;
      color: #0b3b2c;
      line-height: 1.1;
    }

    .logo-text span {
      font-size: 0.8rem;
      font-weight: 500;
      color: #4b6b5e;
      letter-spacing: 0.5px;
    }

    .nav-links {
      display: flex;
      gap: 36px;
      align-items: center;
    }

    .nav-links a {
      text-decoration: none;
      font-weight: 600;
      color: #1e2f28;
      transition: 0.2s;
      font-size: 0.95rem;
      position: relative;
      padding: 4px 0;
    }

    .nav-links a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0%;
      height: 3px;
      background: linear-gradient(90deg, #0b3b2c, #facc15);
      border-radius: 6px;
      transition: 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .nav-links a:hover::after {
      width: 100%;
    }

    .nav-links a:hover {
      color: #0b3b2c;
    }

    .btn-3d {
      background: linear-gradient(145deg, #0b3b2c, #1b5f4a);
      color: #fff !important;
      padding: 12px 32px !important;
      border-radius: 60px;
      font-weight: 700;
      box-shadow: 0 8px 24px rgba(11, 59, 44, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.15);
      transition: 0.25s ease;
      border: none;
    }

    .btn-3d::after {
      display: none !important;
    }

    .btn-3d:hover {
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 14px 36px rgba(11, 59, 44, 0.45);
    }

    /* ===== HERO 3D ===== */
    .hero-3d {
      padding: 60px 0 50px;
      background: radial-gradient(circle at 10% 30%, #e6f0eb, #d5e3dc);
      position: relative;
      overflow: hidden;
    }

    .hero-3d::before {
      content: '';
      position: absolute;
      top: -30%;
      right: -10%;
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(250, 204, 21, 0.08), transparent 70%);
      border-radius: 50%;
      pointer-events: none;
    }

    .hero-grid-3d {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 50px;
      align-items: center;
      position: relative;
    }

    .hero-left h2 {
      font-size: 3.6rem;
      font-weight: 800;
      line-height: 1.15;
      letter-spacing: -1.5px;
      color: #0b2a20;
      animation: slideInLeft 0.9s ease-out;
    }

    .hero-left h2 .highlight-yellow {
      background: linear-gradient(120deg, #facc15 0%, #facc15 40%, transparent 80%);
      padding: 0 12px 0 4px;
      display: inline-block;
      border-radius: 4px;
    }

    .hero-left p {
      font-size: 1.15rem;
      color: #1f4033;
      max-width: 90%;
      margin: 20px 0 28px;
      font-weight: 500;
      opacity: 0.9;
      animation: slideInLeft 1s ease-out;
    }

    .hero-stats-3d {
      display: flex;
      gap: 48px;
      animation: slideInLeft 1.1s ease-out;
    }

    .stat-block {
      background: rgba(255, 255, 255, 0.6);
      backdrop-filter: blur(8px);
      padding: 14px 28px 14px 24px;
      border-radius: 60px;
      border: 1px solid rgba(255, 255, 255, 0.8);
      box-shadow: 0 10px 28px -8px rgba(0, 0, 0, 0.06);
    }

    .stat-block .number {
      font-size: 2.2rem;
      font-weight: 800;
      color: #0b3b2c;
      margin-right: 6px;
    }

    .stat-block .label {
      font-weight: 600;
      color: #2d4d3e;
    }

    .hero-right {
      display: flex;
      justify-content: center;
      animation: slideInRight 0.9s ease-out;
    }

    .card-3d-floating {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(16px);
      border-radius: 48px;
      padding: 40px 36px;
      box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.6);
      transform: perspective(800px) rotateY(-6deg) rotateX(4deg);
      animation: float3d 6s ease-in-out infinite;
      border: 1px solid rgba(255, 255, 255, 0.5);
      max-width: 380px;
    }

    .card-3d-floating .icon-big {
      background: linear-gradient(145deg, #0b3b2c, #1f6b54);
      width: 80px;
      height: 80px;
      border-radius: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.6rem;
      color: #facc15;
      margin-bottom: 20px;
      box-shadow: 0 12px 28px rgba(11, 59, 44, 0.25);
    }

    .card-3d-floating h3 {
      font-size: 1.6rem;
      font-weight: 800;
      color: #0b2a20;
    }

    .card-3d-floating p {
      color: #2d4d3e;
      font-weight: 500;
      margin: 8px 0 16px;
    }

    .chip-list {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .chip-list span {
      background: #eef6f3;
      padding: 6px 18px;
      border-radius: 60px;
      font-weight: 600;
      font-size: 0.85rem;
      color: #0b3b2c;
    }

    /* ===== SECTION TITLE ===== */
    .section-title-3d {
      text-align: center;
      margin-bottom: 48px;
    }

    .section-title-3d h3 {
      font-size: 2.8rem;
      font-weight: 800;
      color: #0b2a20;
      letter-spacing: -1px;
    }

    .section-title-3d h3 i {
      color: #facc15;
    }

    .section-title-3d p {
      color: #3d5a4e;
      font-weight: 500;
      max-width: 560px;
      margin: 8px auto 0;
    }

    /* ===== PRODUCTS 3D ===== */
    .products-3d {
      padding: 50px 0 40px;
    }

    .product-grid-3d {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
      gap: 32px;
    }

    .product-card-3d {
      background: #ffffff;
      border-radius: 32px;
      padding: 28px 20px 24px;
      text-align: center;
      box-shadow: 0 12px 28px -10px rgba(0, 0, 0, 0.04), 0 0 0 1px rgba(0, 0, 0, 0.01);
      transition: 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      position: relative;
      border: 1px solid rgba(255, 255, 255, 0.4);
      transform: perspective(600px) rotateX(0deg) rotateY(0deg);
    }

    .product-card-3d:hover {
      transform: perspective(600px) rotateX(2deg) rotateY(4deg) translateY(-12px);
      box-shadow: 0 32px 64px -16px rgba(11, 59, 44, 0.18), 0 0 0 1px #e2ede8;
    }

    .product-card-3d .badge-3d {
      position: absolute;
      top: 16px;
      right: 16px;
      background: linear-gradient(135deg, #facc15, #f5b700);
      color: #0b2a20;
      font-weight: 800;
      font-size: 0.7rem;
      padding: 4px 18px;
      border-radius: 40px;
      letter-spacing: 0.5px;
      box-shadow: 0 4px 12px rgba(250, 204, 21, 0.3);
    }

    .product-card-3d .icon-3d {
      width: 96px;
      height: 96px;
      background: linear-gradient(145deg, #eaf3ef, #dce8e2);
      border-radius: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3rem;
      color: #0b3b2c;
      margin: 0 auto 16px;
      transition: 0.3s;
      box-shadow: inset 0 -4px 8px rgba(0, 0, 0, 0.02);
    }

    .product-card-3d:hover .icon-3d {
      background: linear-gradient(145deg, #0b3b2c, #1f6b54);
      color: #facc15;
      transform: scale(1.04) rotate(-2deg);
    }

    .product-card-3d h4 {
      font-weight: 700;
      font-size: 1.05rem;
      color: #0b2a20;
    }

    .product-card-3d .price-3d {
      font-weight: 800;
      font-size: 1.4rem;
      color: #0b3b2c;
      margin: 6px 0 12px;
    }

    .btn-3d-outline {
      display: inline-block;
      background: transparent;
      border: 2px solid #0b3b2c;
      color: #0b3b2c;
      padding: 8px 28px;
      border-radius: 60px;
      font-weight: 700;
      font-size: 0.85rem;
      transition: 0.25s;
      text-decoration: none;
    }

    .btn-3d-outline:hover {
      background: #0b3b2c;
      color: #fff;
      box-shadow: 0 8px 20px rgba(11, 59, 44, 0.2);
    }

    /* ===== BUSINESS MODEL 3D ===== */
    .model-3d {
      padding: 60px 0;
      background: #ffffff;
      border-radius: 60px 60px 0 0;
      margin-top: 20px;
      box-shadow: 0 -8px 40px rgba(0, 0, 0, 0.02);
    }

    .model-grid-3d {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 28px;
    }

    .model-item-3d {
      background: #f6faf8;
      border-radius: 28px;
      padding: 32px 24px;
      text-align: center;
      border: 1px solid #e4ede8;
      transition: 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      transform: perspective(400px) rotateX(0);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.02);
    }

    .model-item-3d:hover {
      transform: perspective(400px) rotateX(2deg) translateY(-8px);
      box-shadow: 0 24px 48px -16px rgba(11, 59, 44, 0.12);
      border-color: #bdd6cb;
      background: #ffffff;
    }

    .model-item-3d .icon-3d-model {
      width: 72px;
      height: 72px;
      background: linear-gradient(145deg, #e3eee8, #d0e0d8);
      border-radius: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.2rem;
      color: #0b3b2c;
      margin: 0 auto 16px;
      transition: 0.3s;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.02);
    }

    .model-item-3d:hover .icon-3d-model {
      background: #0b3b2c;
      color: #facc15;
      transform: scale(1.06);
    }

    .model-item-3d h5 {
      font-weight: 700;
      font-size: 1.2rem;
    }

    .model-item-3d p {
      color: #3d5a4e;
      font-weight: 500;
      font-size: 0.95rem;
      margin-top: 4px;
    }

    /* ===== ABOUT 3D ===== */
    .about-3d {
      padding: 50px 0 60px;
    }

    .about-grid-3d {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 48px;
      align-items: center;
    }

    .about-left h3 {
      font-size: 2.6rem;
      font-weight: 800;
      color: #0b2a20;
      line-height: 1.2;
    }

    .about-left h3 i {
      color: #facc15;
    }

    .about-left p {
      color: #2d4d3e;
      font-weight: 500;
      margin: 18px 0;
    }

    .about-card-3d {
      background: #ffffff;
      border-radius: 32px;
      padding: 28px 32px;
      box-shadow: 0 20px 48px -16px rgba(0, 0, 0, 0.04);
      border-left: 8px solid #facc15;
      transition: 0.3s;
    }

    .about-card-3d:hover {
      transform: scale(1.01);
      box-shadow: 0 32px 64px -20px rgba(11, 59, 44, 0.08);
    }

    .about-right {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .about-right .block-3d {
      background: white;
      border-radius: 28px;
      padding: 28px 32px;
      box-shadow: 0 10px 32px -8px rgba(0, 0, 0, 0.02);
      border: 1px solid #eaf1ed;
      transition: 0.3s;
    }

    .about-right .block-3d:hover {
      transform: translateY(-6px);
      box-shadow: 0 24px 48px -12px rgba(11, 59, 44, 0.06);
    }

    .about-right .block-3d i {
      font-size: 2rem;
      color: #0b3b2c;
      background: #e9f2ef;
      padding: 14px;
      border-radius: 20px;
      margin-right: 16px;
    }

    /* ===== FOOTER 3D ===== */
    .footer-3d {
      background: linear-gradient(145deg, #0a2a1f, #08231a);
      color: #c7dbd2;
      padding: 48px 0 20px;
      border-radius: 48px 48px 0 0;
      margin-top: 30px;
      box-shadow: 0 -12px 40px rgba(0, 0, 0, 0.06);
    }

    .footer-grid-3d {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      gap: 48px;
    }

    .footer-grid-3d h4 {
      color: #fff;
      font-weight: 700;
      margin-bottom: 14px;
      font-size: 1.1rem;
    }

    .footer-grid-3d a {
      color: #b0cbc0;
      text-decoration: none;
      display: block;
      margin: 8px 0;
      transition: 0.2s;
      font-weight: 500;
    }

    .footer-grid-3d a:hover {
      color: #facc15;
      transform: translateX(6px);
    }

    .footer-copy {
      border-top: 1px solid #2d4d3e;
      margin-top: 30px;
      padding-top: 20px;
      text-align: center;
      font-size: 0.9rem;
      color: #8dad9e;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
      .hero-grid-3d, .about-grid-3d, .footer-grid-3d {
        grid-template-columns: 1fr;
      }
      .model-grid-3d {
        grid-template-columns: 1fr 1fr;
      }
      .hero-left h2 {
        font-size: 2.8rem;
      }
      .nav-links {
        display: none;
      }
    }

    @media (max-width: 680px) {
      .model-grid-3d {
        grid-template-columns: 1fr;
      }
      .hero-stats-3d {
        flex-wrap: wrap;
        gap: 12px;
      }
      .hero-left h2 {
        font-size: 2.2rem;
      }
      .card-3d-floating {
        padding: 28px 24px;
      }
      .stat-block {
        padding: 10px 20px;
      }
    }

    /* ===== SCROLL REVEAL ===== */
    .reveal-3d {
      opacity: 0;
      transform: translateY(40px) scale(0.96);
      transition: 0.7s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .reveal-3d.active {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  </style>
</head>
<body>

<!-- ===== TOP TICKER ===== -->
<div class="top-ticker">
  <div class="container" style="overflow: hidden; padding: 0 28px;">
    <div class="ticker-track">
      <div class="ticker-item"><i class="fas fa-shopping-bag"></i> <strong>Vũ Thị Lan</strong> <span class="amount">1.890.000₫</span></div>
      <div class="ticker-item"><i class="fas fa-shopping-bag"></i> <strong>NGUYEN THI HUYNH TIEN</strong> <span class="amount">1.890.000₫</span></div>
      <div class="ticker-item"><i class="fas fa-shopping-bag"></i> <strong>NGUYỄN THỊ ĐIỂM</strong> <span class="amount">1.674.000₫</span></div>
      <div class="ticker-item"><i class="fas fa-shopping-bag"></i> <strong>Nguyễn Văn Dĩnh</strong> <span class="amount">2.214.000₫</span></div>
      <div class="ticker-item"><i class="fas fa-shopping-bag"></i> <strong>Tô thị thanh Giang</strong> <span class="amount">1.890.000₫</span></div>
      <div class="ticker-item"><i class="fas fa-shopping-bag"></i> <strong>NGUYỄN THỊ HOÀN</strong> <span class="amount">1.683.000₫</span></div>
      <div class="ticker-item"><i class="fas fa-shopping-bag"></i> <strong>Lộc thị diệu Linh</strong> <span class="amount">1.890.000₫</span></div>
      <div class="ticker-item"><i class="fas fa-shopping-bag"></i> <strong>TRỊNH NGUYỆT</strong> <span class="amount">1.890.000₫</span></div>
      <!-- lặp để vô tận -->
      <div class="ticker-item"><i class="fas fa-shopping-bag"></i> <strong>Vũ Thị Lan</strong> <span class="amount">1.890.000₫</span></div>
      <div class="ticker-item"><i class="fas fa-shopping-bag"></i> <strong>NGUYEN THI HUYNH TIEN</strong> <span class="amount">1.890.000₫</span></div>
      <div class="ticker-item"><i class="fas fa-shopping-bag"></i> <strong>NGUYỄN THỊ ĐIỂM</strong> <span class="amount">1.674.000₫</span></div>
      <div class="ticker-item"><i class="fas fa-shopping-bag"></i> <strong>Nguyễn Văn Dĩnh</strong> <span class="amount">2.214.000₫</span></div>
    </div>
  </div>
</div>

<!-- ===== HEADER ===== -->
<header>
  <div class="container header-grid">
    <div class="logo-block">
      <div class="logo-icon"><i class="fas fa-tree"></i></div>
      <div class="logo-text">
        <h1>Bình An</h1>
        <span>Hệ sinh thái tiêu dùng</span>
      </div>
    </div>
    <div class="nav-links">
      <a href="#">Trang chủ</a>
      <a href="#">Sản phẩm</a>
      <a href="#">Mô hình</a>
      <a href="#">Giới thiệu</a>
      <a href="#" class="btn-3d"><i class="fas fa-store"></i> Mua ngay</a>
    </div>
  </div>
</header>

<!-- ===== HERO 3D ===== -->
<section class="hero-3d">
  <div class="container hero-grid-3d">
    <div class="hero-left">
      <h2>Kiến tạo <br><span class="highlight-yellow">niềm tin thật</span></h2>
      <p>Bảo vệ người tiêu dùng bằng sự minh bạch từ nguồn cung đến sản phẩm. Mỗi combo là một giải pháp tiêu dùng thông minh.</p>
      <div class="hero-stats-3d">
        <div class="stat-block"><span class="number">03+</span><span class="label">Năm</span></div>
        <div class="stat-block"><span class="number">20K+</span><span class="label">Thành viên</span></div>
        <div class="stat-block"><span class="number">50+</span><span class="label">Sản phẩm</span></div>
      </div>
    </div>
    <div class="hero-right">
      <div class="card-3d-floating">
        <div class="icon-big"><i class="fas fa-shield-halved"></i></div>
        <h3>Giá trị đúng</h3>
        <p>Không thổi phồng, không đánh tráo khái niệm</p>
        <div class="chip-list">
          <span>Thật</span>
          <span>Sạch</span>
          <span>Minh bạch</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== PRODUCTS 3D ===== -->
<section class="products-3d container">
  <div class="section-title-3d reveal-3d">
    <h3><i class="fas fa-cube"></i> Sản phẩm nổi bật</h3>
    <p>Combo giải pháp hoàn chỉnh – tối ưu chi phí, đảm bảo chất lượng</p>
  </div>
  <div class="product-grid-3d">
    <div class="product-card-3d reveal-3d">
      <span class="badge-3d">HOT</span>
      <div class="icon-3d"><i class="fas fa-mug-saucer"></i></div>
      <h4>Trà trái cây Đào</h4>
      <div class="price-3d">166.666₫</div>
      <a href="#" class="btn-3d-outline">Xem chi tiết</a>
    </div>
    <div class="product-card-3d reveal-3d">
      <span class="badge-3d">HOT</span>
      <div class="icon-3d"><i class="fas fa-apple-whole"></i></div>
      <h4>Trà trái cây Ổi</h4>
      <div class="price-3d">166.666₫</div>
      <a href="#" class="btn-3d-outline">Xem chi tiết</a>
    </div>
    <div class="product-card-3d reveal-3d">
      <span class="badge-3d">HOT</span>
      <div class="icon-3d"><i class="fas fa-seedling"></i></div>
      <h4>BESTNUT GOLD 900g</h4>
      <div class="price-3d">1.000.000₫</div>
      <a href="#" class="btn-3d-outline">Xem chi tiết</a>
    </div>
    <div class="product-card-3d reveal-3d">
      <span class="badge-3d">HOT</span>
      <div class="icon-3d"><i class="fas fa-coffee-bean"></i></div>
      <h4>Cà phê Thảo Mộc</h4>
      <div class="price-3d">189.000₫</div>
      <a href="#" class="btn-3d-outline">Xem chi tiết</a>
    </div>
    <div class="product-card-3d reveal-3d">
      <span class="badge-3d">HOT</span>
      <div class="icon-3d"><i class="fas fa-cubes"></i></div>
      <h4>Combo 4: BESTNUTGOLD</h4>
      <div class="price-3d">2.214.000₫</div>
      <a href="#" class="btn-3d-outline">Xem chi tiết</a>
    </div>
  </div>
</section>

<!-- ===== BUSINESS MODEL 3D ===== -->
<section class="model-3d">
  <div class="container">
    <div class="section-title-3d reveal-3d">
      <h3><i class="fas fa-cube"></i> Mô hình vận hành</h3>
      <p>6 trụ cột xây dựng niềm tin và giá trị bền vững</p>
    </div>
    <div class="model-grid-3d">
      <div class="model-item-3d reveal-3d"><div class="icon-3d-model"><i class="fas fa-check-double"></i></div><h5>Sản phẩm tốt</h5><p>Chọn lọc chất lượng thật, nguồn gốc rõ ràng.</p></div>
      <div class="model-item-3d reveal-3d"><div class="icon-3d-model"><i class="fas fa-tag"></i></div><h5>Giá trị đúng</h5><p>Bán đúng giá, không thổi phồng.</p></div>
      <div class="model-item-3d reveal-3d"><div class="icon-3d-model"><i class="fas fa-eye"></i></div><h5>Minh bạch</h5><p>Rõ ràng từ sản phẩm đến giao dịch.</p></div>
      <div class="model-item-3d reveal-3d"><div class="icon-3d-model"><i class="fas fa-user-shield"></i></div><h5>Bảo vệ NTD</h5><p>Nói không với hàng giả, kém chất lượng.</p></div>
      <div class="model-item-3d reveal-3d"><div class="icon-3d-model"><i class="fas fa-handshake"></i></div><h5>Phân phối có trách nhiệm</h5><p>Đến đúng người cần, đúng giải pháp.</p></div>
      <div class="model-item-3d reveal-3d"><div class="icon-3d-model"><i class="fas fa-tree"></i></div><h5>Phát triển bền vững</h5><p>Dài hạn dựa trên niềm tin.</p></div>
    </div>
  </div>
</section>

<!-- ===== ABOUT 3D ===== -->
<section class="about-3d container">
  <div class="about-grid-3d">
    <div class="about-left reveal-3d">
      <h3>Hệ sinh thái <br><i>Bình An</i></h3>
      <p>Kế thừa 3 năm kinh nghiệm, chúng tôi tiên phong trong bảo vệ người tiêu dùng, sàng lọc từ nguồn cung và loại bỏ hàng giả.</p>
      <div class="about-card-3d">
        <i class="fas fa-quote-left" style="color:#0b3b2c; font-size:1.8rem; opacity:0.5;"></i>
        <p style="font-weight:600; margin-top: 4px; color:#0b2a20;">“Đưa sản phẩm đúng giá trị ra thị trường, kiến tạo cách tiêu dùng thông minh.”</p>
      </div>
    </div>
    <div class="about-right reveal-3d">
      <div class="block-3d">
        <i class="fas fa-building"></i> <strong style="font-size:1.1rem;">Công ty CP Hệ sinh thái Bình An</strong>
        <p style="color:#2d4d3e; margin-top:6px;">Tự chủ nguồn sản phẩm, minh bạch trong hợp tác.</p>
      </div>
      <div class="block-3d">
        <i class="fas fa-people-group"></i> <strong style="font-size:1.1rem;">Cộng đồng 20.000+ thành viên</strong>
        <p style="color:#2d4d3e; margin-top:6px;">Cùng nhau xây dựng niềm tin và giá trị thật.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== FOOTER 3D ===== -->
<footer class="footer-3d">
  <div class="container footer-grid-3d">
    <div>
      <h4><i class="fas fa-tree" style="color:#facc15; margin-right:8px;"></i> Bình An</h4>
      <p>Hệ sinh thái tiêu dùng thông minh – Bảo vệ người tiêu dùng bằng sự minh bạch.</p>
    </div>
    <div>
      <h4>Điều hướng</h4>
      <a href="#">Trang chủ</a>
      <a href="#">Sản phẩm</a>
      <a href="#">Giới thiệu</a>
    </div>
    <div>
      <h4>Kết nối</h4>
      <a href="#"><i class="fas fa-phone-alt" style="width:20px;"></i> 0909 123 456</a>
      <a href="#"><i class="fas fa-envelope" style="width:20px;"></i> info@binhan.vn</a>
      <a href="#"><i class="fas fa-map-pin" style="width:20px;"></i> TP. Hồ Chí Minh</a>
    </div>
  </div>
  <div class="container footer-copy">
    &copy; 2026 Công ty CP Hệ sinh thái Bình An – Thiết kế 3D đậm chất chuyên nghiệp.
  </div>
</footer>

<!-- ===== SCROLL REVEAL ===== -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const reveals = document.querySelectorAll('.reveal-3d');
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active');
        }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -30px 0px' });
    reveals.forEach(el => observer.observe(el));
  });
</script>

</body>
</html>