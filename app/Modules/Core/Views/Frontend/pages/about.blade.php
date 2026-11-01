@extends('Core::Frontend.layouts.app')

@section('title', 'Giới thiệu')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $about?->content), 160))

@php
    $video = $site['video_gioi_thieu_ve_chung_toi'] ?? $site['video_kinhdoanhbinhan'] ?? $site['video_hoptacxa'] ?? null;
    $stats = array_filter([
        'Năm kinh doanh' => $about?->nam_kinh_doanh,
        'Khách hàng'     => $about?->khach_hang,
        'Sản phẩm'       => $about?->san_pham,
    ]);
@endphp

@push('styles')
<style>
    .about-hero-img { border-radius: 22px; overflow: hidden; box-shadow: 0 20px 50px rgba(16,48,45,.15); }
    .about-hero-img img, .about-hero-img video { width: 100%; display: block; }
    .stat-box { background: var(--brand-gradient); color: #fff; border-radius: 18px; padding: 26px; text-align: center; height: 100%; }
    .stat-box .num { font-size: 2.2rem; font-weight: 800; line-height: 1; }
    .value-card { background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 24px; height: 100%; }
    .value-card .ic { width: 52px; height: 52px; border-radius: 14px; background: var(--primary-soft); color: var(--primary); display: grid; place-items: center; font-size: 22px; margin-bottom: 14px; }
    .story-card { background: #fff; border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden; height: 100%; }
    .story-card img { width: 100%; aspect-ratio: 16 / 10; object-fit: cover; }
    .leader-card { text-align: center; background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 22px 16px; height: 100%; }
    .leader-card img { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-soft); }
</style>
@endpush

@section('content')
<section class="page-hero">
    <div class="container">
        <h1>Giới thiệu</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Trang chủ</a></li><li class="breadcrumb-item active">Giới thiệu</li></ol></nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="about-hero-img">
                    @if($video)
                        <video src="{{ cdn($video) }}" controls playsinline preload="metadata" poster="{{ $about?->image ? cdn($about->image) : '' }}"></video>
                    @elseif($about?->image)
                        <img src="{{ cdn($about->image) }}" alt="{{ $about->name }}">
                    @else
                        <img src="{{ $site['logo_url'] }}" alt="{{ $site['name'] ?? '' }}" style="padding:60px;background:#fff">
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                <span class="eyebrow">Về chúng tôi</span>
                <h2 class="fw-bold mb-3">{{ $about?->name ?? ($site['name'] ?? '') }}</h2>
                <div class="rich-content">{!! clean_html($about?->content ?: '<p>Thông tin giới thiệu đang được cập nhật.</p>') !!}</div>
            </div>
        </div>

        @if($stats)
            <div class="row g-3 mt-4">
                @foreach($stats as $label => $value)
                    <div class="col-md-4"><div class="stat-box"><div class="num">{{ $value }}</div><div class="mt-2">{{ $label }}</div></div></div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="section bg-white">
    <div class="container">
        <div class="section-title justify-content-center text-center"><div><span class="eyebrow">Giá trị cốt lõi</span><h2>Điều chúng tôi <span>cam kết</span></h2></div></div>
        <div class="row g-3">
            @foreach([
                ['fa-leaf', 'Chất lượng', 'Sản phẩm có nguồn gốc rõ ràng, đầy đủ giấy tờ, chứng nhận theo quy định.'],
                ['fa-handshake', 'Minh bạch', 'Giá bán, chính sách vận chuyển, đổi trả được công khai rõ ràng.'],
                ['fa-heart', 'Tận tâm', 'Lắng nghe và hỗ trợ khách hàng trước, trong và sau khi mua hàng.'],
                ['fa-people-group', 'Cộng đồng', 'Phát triển bền vững cùng khách hàng, đối tác và cộng đồng.'],
            ] as [$icon, $title, $desc])
                <div class="col-sm-6 col-lg-3"><div class="value-card"><div class="ic"><i class="fas {{ $icon }}"></i></div><h5 class="fw-bold">{{ $title }}</h5><p class="text-muted mb-0">{{ $desc }}</p></div></div>
            @endforeach
        </div>
    </div>
</section>

@if($stories->isNotEmpty())
<section class="section">
    <div class="container">
        <div class="section-title"><div><span class="eyebrow">Hành trình</span><h2>Câu chuyện <span>của chúng tôi</span></h2></div></div>
        <div class="row g-4">
            @foreach($stories as $story)
                <div class="col-md-6 col-lg-3">
                    <div class="story-card">
                        @if($story->image)<img src="{{ cdn($story->image) }}" alt="{{ $story->name }}" loading="lazy">@endif
                        <div class="p-3">
                            @if($story->year)<span class="badge bg-brand-soft text-brand mb-2">{{ $story->year }}</span>@endif
                            <h6 class="fw-bold">{{ $story->name }}</h6>
                            <p class="text-muted small mb-0">{{ \Illuminate\Support\Str::limit(strip_tags((string) $story->content), 140) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($leaders->isNotEmpty())
<section class="section bg-white">
    <div class="container">
        <div class="section-title justify-content-center text-center"><div><span class="eyebrow">Đội ngũ</span><h2>Ban <span>lãnh đạo</span></h2></div></div>
        <div class="row g-3 justify-content-center">
            @foreach($leaders as $leader)
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

<section class="section">
    <div class="container">
        <div class="section-title"><div><h2>Liên hệ <span>với chúng tôi</span></h2></div></div>
        @include('Core::Frontend.pages.partials.contact-channels')
    </div>
</section>
@endsection
