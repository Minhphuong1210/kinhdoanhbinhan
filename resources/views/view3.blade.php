<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bình An – Thiên nhiên & Niềm tin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f6f3;
            color: #0f2a20;
            overflow-x: hidden;
        }

        .container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 28px;
        }

        /* ===== ANIMATION MỚI ===== */
        @keyframes floatLeaf {
            0% {
                transform: translateY(0px) rotate(0deg) scale(1);
            }
            50% {
                transform: translateY(-25px) rotate(8deg) scale(1.02);
            }
            100% {
                transform: translateY(0px) rotate(0deg) scale(1);
            }
        }

        @keyframes waveOrbit {
            0% {
                transform: translateX(0) rotate(0deg);
            }
            50% {
                transform: translateX(40px) rotate(5deg);
            }
            100% {
                transform: translateX(0) rotate(0deg);
            }
        }

        @keyframes morphBlob {
            0% {
                border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
            }
            50% {
                border-radius: 30% 60% 70% 40% / 50% 60% 30% 70%;
            }
            100% {
                border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
            }
        }

        @keyframes slideUpStagger {
            0% {
                opacity: 0;
                transform: translateY(70px) scale(0.9);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes rotateCube {
            0% {
                transform: perspective(800px) rotateY(0deg) rotateX(5deg);
            }
            100% {
                transform: perspective(800px) rotateY(360deg) rotateX(5deg);
            }
        }

        @keyframes shimmerGreen {
            0% {
                background-position: -200% center;
            }
            100% {
                background-position: 200% center;
            }
        }

        @keyframes tickerGreen {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }

        @keyframes pulseRing {
            0% {
                box-shadow: 0 0 0 0 rgba(11, 59, 44, 0.2);
            }
            70% {
                box-shadow: 0 0 0 30px rgba(11, 59, 44, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(11, 59, 44, 0);
            }
        }

        @keyframes slideDiagonal {
            0% {
                opacity: 0;
                transform: translate(-40px, 40px) rotate(-3deg);
            }
            100% {
                opacity: 1;
                transform: translate(0, 0) rotate(0);
            }
        }

        /* ===== TICKER ===== */
        .ticker-green {
            background: linear-gradient(135deg, #0b3b2c, #1a5e4a);
            padding: 14px 0;
            overflow: hidden;
            border-bottom: 4px solid #facc15;
            position: relative;
        }

        .ticker-green::after {
            content: '🌿';
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.3rem;
            z-index: 2;
            animation: floatLeaf 3s ease-in-out infinite;
        }

        .ticker-track-green {
            display: flex;
            gap: 50px;
            animation: tickerGreen 40s linear infinite;
            width: max-content;
            padding-left: 60px;
        }

        .ticker-item-green {
            display: flex;
            align-items: center;
            gap: 14px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(8px);
            padding: 6px 24px 6px 20px;
            border-radius: 40px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .ticker-item-green i {
            color: #facc15;
            font-size: 1rem;
        }

        .ticker-item-green strong {
            color: #fff;
            font-weight: 700;
        }

        .ticker-item-green .amount-green {
            background: #facc15;
            color: #0b3b2c;
            font-weight: 800;
            padding: 2px 16px;
            border-radius: 40px;
            font-size: 0.85rem;
        }

        /* ===== HEADER ===== */
        header {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 2px solid rgba(11, 59, 44, 0.06);
        }

        .header-green {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-green {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-green .logo-orb {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #0b3b2c, #2a7a5e);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: #facc15;
            animation: morphBlob 6s ease-in-out infinite;
            box-shadow: 0 8px 24px rgba(11, 59, 44, 0.15);
        }

        .logo-green h1 {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #0b3b2c;
        }

        .logo-green h1 span {
            color: #facc15;
        }

        .logo-green p {
            font-size: 0.8rem;
            color: #4a6b5a;
            font-weight: 500;
            margin-top: -2px;
        }

        .nav-green {
            display: flex;
            gap: 36px;
            align-items: center;
        }

        .nav-green a {
            text-decoration: none;
            font-weight: 600;
            color: #1e3d30;
            transition: 0.3s;
            font-size: 0.95rem;
            position: relative;
        }

        .nav-green a::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #0b3b2c, #facc15);
            border-radius: 4px;
            transition: 0.3s;
        }

        .nav-green a:hover::after {
            width: 100%;
        }

        .nav-green a:hover {
            color: #0b3b2c;
        }

        .btn-green {
            background: linear-gradient(135deg, #0b3b2c, #1d6b52);
            color: #fff !important;
            padding: 12px 32px !important;
            border-radius: 40px;
            font-weight: 700;
            box-shadow: 0 6px 20px rgba(11, 59, 44, 0.25);
            transition: 0.3s;
        }

        .btn-green:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 32px rgba(11, 59, 44, 0.35);
        }

        .btn-green::after {
            display: none !important;
        }

        /* ===== HERO MỚI ===== */
        .hero-green {
            padding: 60px 0 50px;
            background: radial-gradient(ellipse at 80% 20%, #dcebe4, #eef6f0);
            position: relative;
            overflow: hidden;
        }

        .hero-green .blob-bg {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(250, 204, 21, 0.06), transparent 70%);
            top: -20%;
            right: -10%;
            animation: morphBlob 8s ease-in-out infinite;
            border-radius: 50%;
        }

        .hero-grid-green {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
            position: relative;
        }

        .hero-left-green .hero-badge {
            display: inline-block;
            background: rgba(11, 59, 44, 0.06);
            padding: 6px 24px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 0.85rem;
            color: #0b3b2c;
            margin-bottom: 16px;
            border: 1px solid rgba(11, 59, 44, 0.06);
            animation: slideDiagonal 0.8s ease-out;
        }

        .hero-left-green h2 {
            font-size: 3.8rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -1.5px;
            color: #0b2a20;
            animation: slideUpStagger 0.9s ease-out;
        }

        .hero-left-green h2 .highlight-leaf {
            background: linear-gradient(120deg, #0b3b2c, #2a7a5e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        .hero-left-green h2 .highlight-gold {
            background: linear-gradient(120deg, #facc15, #f5b700);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-left-green p {
            font-size: 1.15rem;
            color: #2a4d3e;
            max-width: 90%;
            margin: 20px 0 28px;
            font-weight: 500;
            animation: slideUpStagger 1s ease-out;
        }

        .hero-stats-green {
            display: flex;
            gap: 32px;
            animation: slideUpStagger 1.1s ease-out;
        }

        .stat-green {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(12px);
            padding: 14px 28px;
            border-radius: 60px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
            transition: 0.3s;
        }

        .stat-green:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 28px rgba(11, 59, 44, 0.06);
        }

        .stat-green .number-green {
            font-size: 2.2rem;
            font-weight: 800;
            color: #0b3b2c;
        }

        .stat-green .label-green {
            font-weight: 600;
            color: #3a5d4e;
            margin-left: 4px;
        }

        .hero-right-green {
            display: flex;
            justify-content: center;
            animation: slideUpStagger 1.2s ease-out;
        }

        .card-leaf {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border-radius: 50% 50% 40% 60% / 60% 40% 60% 40%;
            padding: 44px 40px 50px;
            box-shadow: 0 30px 60px -20px rgba(11, 59, 44, 0.15), inset 0 0 0 1px rgba(255, 255, 255, 0.6);
            animation: floatLeaf 5s ease-in-out infinite;
            max-width: 400px;
            width: 100%;
            border: 2px solid rgba(250, 204, 21, 0.15);
        }

        .card-leaf .leaf-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #0b3b2c, #2a7a5e);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.4rem;
            color: #facc15;
            margin-bottom: 16px;
            animation: morphBlob 4s ease-in-out infinite;
        }

        .card-leaf h3 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0b2a20;
        }

        .card-leaf p {
            color: #2a4d3e;
            font-weight: 500;
            margin: 8px 0 16px;
        }

        .card-leaf .leaf-tags {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .card-leaf .leaf-tags span {
            background: rgba(11, 59, 44, 0.06);
            padding: 6px 20px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.8rem;
            color: #0b3b2c;
            border: 1px solid rgba(11, 59, 44, 0.04);
        }

        /* ===== SECTION TITLE ===== */
        .section-title-green {
            text-align: center;
            margin-bottom: 48px;
        }

        .section-title-green h3 {
            font-size: 2.8rem;
            font-weight: 800;
            color: #0b2a20;
            letter-spacing: -1px;
        }

        .section-title-green h3 .highlight-gold {
            color: #facc15;
        }

        .section-title-green p {
            color: #3a5d4e;
            font-weight: 500;
            max-width: 560px;
            margin: 8px auto 0;
        }

        /* ===== PRODUCTS MỚI ===== */
        .products-green {
            padding: 50px 0 40px;
        }

        .product-grid-green {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 28px;
        }

        .product-card-green {
            background: #ffffff;
            border-radius: 40% 20% 40% 20% / 20% 40% 20% 40%;
            padding: 28px 20px 24px;
            text-align: center;
            box-shadow: 0 8px 28px -8px rgba(0, 0, 0, 0.02), 0 0 0 1px rgba(0, 0, 0, 0.01);
            transition: 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border: 2px solid rgba(11, 59, 44, 0.04);
            position: relative;
        }

        .product-card-green:hover {
            transform: translateY(-12px) scale(1.02) rotate(-1deg);
            border-radius: 30% 40% 30% 40% / 40% 30% 40% 30%;
            box-shadow: 0 24px 48px -12px rgba(11, 59, 44, 0.08);
            border-color: rgba(250, 204, 21, 0.2);
        }

        .product-card-green .badge-leaf {
            position: absolute;
            top: 14px;
            right: 14px;
            background: linear-gradient(135deg, #facc15, #f5b700);
            color: #0b2a20;
            font-weight: 800;
            font-size: 0.7rem;
            padding: 4px 18px;
            border-radius: 40px;
            box-shadow: 0 4px 12px rgba(250, 204, 21, 0.2);
        }

        .product-card-green .icon-green {
            width: 80px;
            height: 80px;
            background: linear-gradient(145deg, #eaf3ef, #dce8e2);
            border-radius: 40% 60% 40% 60% / 60% 40% 60% 40%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            color: #0b3b2c;
            margin: 0 auto 14px;
            transition: 0.4s;
        }

        .product-card-green:hover .icon-green {
            background: linear-gradient(135deg, #0b3b2c, #2a7a5e);
            color: #facc15;
            transform: rotate(8deg) scale(1.06);
            border-radius: 60% 40% 60% 40% / 40% 60% 40% 60%;
        }

        .product-card-green h4 {
            font-weight: 700;
            font-size: 1.05rem;
            color: #0b2a20;
        }

        .product-card-green .price-green {
            font-weight: 800;
            font-size: 1.4rem;
            color: #0b3b2c;
            margin: 6px 0 14px;
        }

        .btn-green-outline {
            display: inline-block;
            background: transparent;
            border: 2px solid #0b3b2c;
            color: #0b3b2c;
            padding: 8px 28px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 0.85rem;
            transition: 0.3s;
            text-decoration: none;
        }

        .btn-green-outline:hover {
            background: #0b3b2c;
            color: #fff;
            box-shadow: 0 8px 24px rgba(11, 59, 44, 0.15);
            transform: scale(1.02);
        }

        /* ===== BUSINESS MODEL MỚI ===== */
        .model-green {
            padding: 60px 0;
            background: #ffffff;
            border-radius: 60% 60% 0% 0% / 20% 20% 0% 0%;
            margin-top: 20px;
            box-shadow: 0 -8px 40px rgba(0, 0, 0, 0.02);
        }

        .model-grid-green {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .model-item-green {
            background: #f6faf8;
            border-radius: 40% 20% 40% 20% / 20% 40% 20% 40%;
            padding: 32px 24px;
            text-align: center;
            border: 2px solid rgba(11, 59, 44, 0.04);
            transition: 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .model-item-green:hover {
            transform: translateY(-8px) scale(1.02);
            border-radius: 30% 40% 30% 40% / 40% 30% 40% 30%;
            box-shadow: 0 20px 40px -12px rgba(11, 59, 44, 0.06);
            border-color: rgba(250, 204, 21, 0.15);
            background: #ffffff;
        }

        .model-item-green .icon-model-green {
            width: 64px;
            height: 64px;
            background: linear-gradient(145deg, #e3eee8, #d0e0d8);
            border-radius: 40% 60% 40% 60% / 60% 40% 60% 40%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #0b3b2c;
            margin: 0 auto 14px;
            transition: 0.4s;
        }

        .model-item-green:hover .icon-model-green {
            background: #0b3b2c;
            color: #facc15;
            transform: rotate(6deg) scale(1.06);
            border-radius: 60% 40% 60% 40% / 40% 60% 40% 60%;
        }

        .model-item-green h5 {
            font-weight: 700;
            font-size: 1.15rem;
            color: #0b2a20;
        }

        .model-item-green p {
            color: #3a5d4e;
            font-weight: 500;
            font-size: 0.92rem;
            margin-top: 4px;
        }

        /* ===== ABOUT MỚI ===== */
        .about-green {
            padding: 50px 0 60px;
        }

        .about-grid-green {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .about-left-green h3 {
            font-size: 2.8rem;
            font-weight: 800;
            color: #0b2a20;
            line-height: 1.2;
        }

        .about-left-green h3 .highlight-gold {
            color: #facc15;
        }

        .about-left-green p {
            color: #2a4d3e;
            font-weight: 500;
            margin: 18px 0;
            font-size: 1.05rem;
        }

        .about-card-green {
            background: #ffffff;
            border-radius: 40% 20% 40% 20% / 20% 40% 20% 40%;
            padding: 28px 32px;
            box-shadow: 0 12px 32px -12px rgba(0, 0, 0, 0.02);
            border-left: 6px solid #facc15;
            transition: 0.3s;
        }

        .about-card-green:hover {
            transform: scale(1.01);
            border-radius: 30% 40% 30% 40% / 40% 30% 40% 30%;
        }

        .about-right-green {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .about-block-green {
            background: #ffffff;
            border-radius: 30% 50% 30% 50% / 50% 30% 50% 30%;
            padding: 28px 32px;
            box-shadow: 0 8px 24px -6px rgba(0, 0, 0, 0.02);
            border: 2px solid rgba(11, 59, 44, 0.04);
            transition: 0.4s;
        }

        .about-block-green:hover {
            transform: translateY(-6px) scale(1.01);
            border-radius: 40% 30% 40% 30% / 30% 40% 30% 40%;
            border-color: rgba(250, 204, 21, 0.15);
        }

        .about-block-green i {
            font-size: 2rem;
            color: #0b3b2c;
            background: #e9f2ef;
            padding: 14px;
            border-radius: 40% 60% 40% 60% / 60% 40% 60% 40%;
            margin-right: 16px;
        }

        /* ===== FOOTER MỚI ===== */
        .footer-green {
            background: linear-gradient(145deg, #0a2a1f, #061812);
            color: #c7dbd2;
            padding: 48px 0 20px;
            border-radius: 60% 60% 0% 0% / 20% 20% 0% 0%;
            margin-top: 30px;
            box-shadow: 0 -12px 40px rgba(0, 0, 0, 0.04);
        }

        .footer-grid-green {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 48px;
        }

        .footer-grid-green h4 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 14px;
            font-size: 1.1rem;
        }

        .footer-grid-green h4 .gold-dot {
            color: #facc15;
        }

        .footer-grid-green a {
            color: #b0cbc0;
            text-decoration: none;
            display: block;
            margin: 8px 0;
            transition: 0.3s;
            font-weight: 500;
        }

        .footer-grid-green a:hover {
            color: #facc15;
            transform: translateX(6px);
        }

        .footer-copy-green {
            border-top: 1px solid #2d4d3e;
            margin-top: 30px;
            padding-top: 20px;
            text-align: center;
            font-size: 0.9rem;
            color: #7a9b8a;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .hero-grid-green,
            .about-grid-green,
            .footer-grid-green {
                grid-template-columns: 1fr;
            }
            .model-grid-green {
                grid-template-columns: 1fr 1fr;
            }
            .hero-left-green h2 {
                font-size: 2.8rem;
            }
            .nav-green {
                display: none;
            }
        }

        @media (max-width: 680px) {
            .model-grid-green {
                grid-template-columns: 1fr;
            }
            .hero-stats-green {
                flex-wrap: wrap;
                gap: 12px;
            }
            .hero-left-green h2 {
                font-size: 2.2rem;
            }
            .card-leaf {
                padding: 30px 24px;
            }
            .stat-green {
                padding: 10px 20px;
            }
        }

        /* ===== SCROLL REVEAL ===== */
        .reveal-green {
            opacity: 0;
            transform: translateY(50px) scale(0.95);
            transition: 0.7s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .reveal-green.active {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    </style>
</head>
<body>

    <!-- ===== TICKER ===== -->
    <div class="ticker-green">
        <div class="container" style="overflow:hidden; padding:0 28px;">
            <div class="ticker-track-green">
                <div class="ticker-item-green"><i class="fas fa-leaf"></i> <strong>Vũ Thị Lan</strong> <span class="amount-green">1.890.000₫</span></div>
                <div class="ticker-item-green"><i class="fas fa-leaf"></i> <strong>NGUYEN THI HUYNH TIEN</strong> <span class="amount-green">1.890.000₫</span></div>
                <div class="ticker-item-green"><i class="fas fa-leaf"></i> <strong>NGUYỄN THỊ ĐIỂM</strong> <span class="amount-green">1.674.000₫</span></div>
                <div class="ticker-item-green"><i class="fas fa-leaf"></i> <strong>Nguyễn Văn Dĩnh</strong> <span class="amount-green">2.214.000₫</span></div>
                <div class="ticker-item-green"><i class="fas fa-leaf"></i> <strong>Tô thị thanh Giang</strong> <span class="amount-green">1.890.000₫</span></div>
                <div class="ticker-item-green"><i class="fas fa-leaf"></i> <strong>NGUYỄN THỊ HOÀN</strong> <span class="amount-green">1.683.000₫</span></div>
                <div class="ticker-item-green"><i class="fas fa-leaf"></i> <strong>Lộc thị diệu Linh</strong> <span class="amount-green">1.890.000₫</span></div>
                <div class="ticker-item-green"><i class="fas fa-leaf"></i> <strong>TRỊNH NGUYỆT</strong> <span class="amount-green">1.890.000₫</span></div>
                <!-- Lặp -->
                <div class="ticker-item-green"><i class="fas fa-leaf"></i> <strong>Vũ Thị Lan</strong> <span class="amount-green">1.890.000₫</span></div>
                <div class="ticker-item-green"><i class="fas fa-leaf"></i> <strong>NGUYEN THI HUYNH TIEN</strong> <span class="amount-green">1.890.000₫</span></div>
                <div class="ticker-item-green"><i class="fas fa-leaf"></i> <strong>NGUYỄN THỊ ĐIỂM</strong> <span class="amount-green">1.674.000₫</span></div>
                <div class="ticker-item-green"><i class="fas fa-leaf"></i> <strong>Nguyễn Văn Dĩnh</strong> <span class="amount-green">2.214.000₫</span></div>
            </div>
        </div>
    </div>

    <!-- ===== HEADER ===== -->
    <header>
        <div class="container header-green">
            <div class="logo-green">
                <div class="logo-orb"><i class="fas fa-seedling"></i></div>
                <div>
                    <h1>Bình <span>An</span></h1>
                    <p>Hệ sinh thái tiêu dùng</p>
                </div>
            </div>
            <div class="nav-green">
                <a href="#">Trang chủ</a>
                <a href="#">Sản phẩm</a>
                <a href="#">Mô hình</a>
                <a href="#">Giới thiệu</a>
                <a href="#" class="btn-green"><i class="fas fa-store"></i> Mua ngay</a>
            </div>
        </div>
    </header>

    <!-- ===== HERO ===== -->
    <section class="hero-green">
        <div class="blob-bg"></div>
        <div class="container hero-grid-green">
            <div class="hero-left-green">
                <div class="hero-badge"><i class="fas fa-shield-alt"></i> Bảo vệ người tiêu dùng</div>
                <h2>
                    Xây dựng <br />
                    <span class="highlight-leaf">niềm tin</span> từ <br />
                    <span class="highlight-gold">giá trị thật</span>
                </h2>
                <p>Chúng tôi kiến tạo hệ sinh thái tiêu dùng minh bạch – nơi mỗi sản phẩm đều là giá trị thật, bảo vệ quyền lợi người tiêu dùng.</p>
                <div class="hero-stats-green">
                    <div class="stat-green"><span class="number-green">03+</span><span class="label-green">Năm</span></div>
                    <div class="stat-green"><span class="number-green">20K+</span><span class="label-green">Thành viên</span></div>
                    <div class="stat-green"><span class="number-green">50+</span><span class="label-green">Sản phẩm</span></div>
                </div>
            </div>
            <div class="hero-right-green">
                <div class="card-leaf">
                    <div class="leaf-icon"><i class="fas fa-tree"></i></div>
                    <h3>Hệ sinh thái xanh</h3>
                    <p>Minh bạch từ nguồn cung đến sản phẩm</p>
                    <div class="leaf-tags">
                        <span>Thật</span>
                        <span>Sạch</span>
                        <span>Bền vững</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== PRODUCTS ===== -->
    <section class="products-green container">
        <div class="section-title-green reveal-green">
            <h3>Sản phẩm <span class="highlight-gold">nổi bật</span></h3>
            <p>Combo giải pháp hoàn chỉnh – tối ưu chi phí, đảm bảo chất lượng</p>
        </div>
        <div class="product-grid-green">
            <div class="product-card-green reveal-green">
                <span class="badge-leaf">HOT</span>
                <div class="icon-green"><i class="fas fa-mug-saucer"></i></div>
                <h4>Trà trái cây Đào</h4>
                <div class="price-green">166.666₫</div>
                <a href="#" class="btn-green-outline">Xem chi tiết</a>
            </div>
            <div class="product-card-green reveal-green">
                <span class="badge-leaf">HOT</span>
                <div class="icon-green"><i class="fas fa-apple-whole"></i></div>
                <h4>Trà trái cây Ổi</h4>
                <div class="price-green">166.666₫</div>
                <a href="#" class="btn-green-outline">Xem chi tiết</a>
            </div>
            <div class="product-card-green reveal-green">
                <span class="badge-leaf">HOT</span>
                <div class="icon-green"><i class="fas fa-seedling"></i></div>
                <h4>BESTNUT GOLD 900g</h4>
                <div class="price-green">1.000.000₫</div>
                <a href="#" class="btn-green-outline">Xem chi tiết</a>
            </div>
            <div class="product-card-green reveal-green">
                <span class="badge-leaf">HOT</span>
                <div class="icon-green"><i class="fas fa-coffee-bean"></i></div>
                <h4>Cà phê Thảo Mộc</h4>
                <div class="price-green">189.000₫</div>
                <a href="#" class="btn-green-outline">Xem chi tiết</a>
            </div>
            <div class="product-card-green reveal-green">
                <span class="badge-leaf">HOT</span>
                <div class="icon-green"><i class="fas fa-cubes"></i></div>
                <h4>Combo 4: BESTNUTGOLD</h4>
                <div class="price-green">2.214.000₫</div>
                <a href="#" class="btn-green-outline">Xem chi tiết</a>
            </div>
        </div>
    </section>

    <!-- ===== BUSINESS MODEL ===== -->
    <section class="model-green">
        <div class="container">
            <div class="section-title-green reveal-green">
                <h3>Mô hình <span class="highlight-gold">vận hành</span></h3>
                <p>6 trụ cột xây dựng niềm tin và giá trị bền vững</p>
            </div>
            <div class="model-grid-green">
                <div class="model-item-green reveal-green"><div class="icon-model-green"><i class="fas fa-check-double"></i></div><h5>Sản phẩm tốt</h5><p>Chọn lọc chất lượng thật, nguồn gốc rõ ràng.</p></div>
                <div class="model-item-green reveal-green"><div class="icon-model-green"><i class="fas fa-tag"></i></div><h5>Giá trị đúng</h5><p>Bán đúng giá, không thổi phồng.</p></div>
                <div class="model-item-green reveal-green"><div class="icon-model-green"><i class="fas fa-eye"></i></div><h5>Minh bạch</h5><p>Rõ ràng từ sản phẩm đến giao dịch.</p></div>
                <div class="model-item-green reveal-green"><div class="icon-model-green"><i class="fas fa-user-shield"></i></div><h5>Bảo vệ NTD</h5><p>Nói không với hàng giả, kém chất lượng.</p></div>
                <div class="model-item-green reveal-green"><div class="icon-model-green"><i class="fas fa-handshake"></i></div><h5>Phân phối có trách nhiệm</h5><p>Đến đúng người cần, đúng giải pháp.</p></div>
                <div class="model-item-green reveal-green"><div class="icon-model-green"><i class="fas fa-tree"></i></div><h5>Phát triển bền vững</h5><p>Dài hạn dựa trên niềm tin.</p></div>
            </div>
        </div>
    </section>

    <!-- ===== ABOUT ===== -->
    <section class="about-green container">
        <div class="about-grid-green">
            <div class="about-left-green reveal-green">
                <h3>Hệ sinh thái <br /><span class="highlight-gold">Bình An</span></h3>
                <p>Kế thừa 3 năm kinh nghiệm, chúng tôi tiên phong trong bảo vệ người tiêu dùng, sàng lọc từ nguồn cung và loại bỏ hàng giả.</p>
                <div class="about-card-green">
                    <i class="fas fa-quote-left" style="color:#0b3b2c; font-size:1.8rem; opacity:0.5;"></i>
                    <p style="font-weight:600; margin-top:4px; color:#0b2a20;">"Đưa sản phẩm đúng giá trị ra thị trường, kiến tạo cách tiêu dùng thông minh."</p>
                </div>
            </div>
            <div class="about-right-green reveal-green">
                <div class="about-block-green">
                    <i class="fas fa-building"></i> <strong style="font-size:1.1rem;">Công ty CP Hệ sinh thái Bình An</strong>
                    <p style="color:#2a4d3e; margin-top:6px;">Tự chủ nguồn sản phẩm, minh bạch trong hợp tác.</p>
                </div>
                <div class="about-block-green">
                    <i class="fas fa-people-group"></i> <strong style="font-size:1.1rem;">Cộng đồng 20.000+ thành viên</strong>
                    <p style="color:#2a4d3e; margin-top:6px;">Cùng nhau xây dựng niềm tin và giá trị thật.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer-green">
        <div class="container footer-grid-green">
            <div>
                <h4><i class="fas fa-seedling" style="color:#facc15; margin-right:8px;"></i> Bình <span class="gold-dot">An</span></h4>
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
        <div class="container footer-copy-green">
            &copy; 2026 Công ty CP Hệ sinh thái Bình An – Thiết kế với tình yêu thiên nhiên.
        </div>
    </footer>

    <!-- ===== SCROLL REVEAL ===== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reveals = document.querySelectorAll('.reveal-green');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, { threshold: 0.15 });
            reveals.forEach(el => observer.observe(el));
        });
    </script>

</body>
</html>