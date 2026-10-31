@extends('Core::Frontend.layouts.app')

@section('title', $post->meta_title ?: $post->name)
@section('meta_description', $post->meta_description ?: $post->description)

@section('extra-styles')
<style>
    .page-hero {
        background: linear-gradient(135deg, #1a3a6b, #2980b9);
        color: #fff; padding: 36px 0 30px; text-align: center;
    }
    .page-hero h1 { font-size: 1.6rem; font-weight: 800; }
    .page-hero .breadcrumb { justify-content: center; background: none; padding: 0; margin: 8px 0 0; }
    .page-hero .breadcrumb-item, .page-hero .breadcrumb-item a { color: rgba(255,255,255,.75); font-size: .875rem; text-decoration: none; }
    .page-hero .breadcrumb-item.active { color: #fff; }
    .page-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.5); }

    .post-content {
        font-size: 1rem; line-height: 1.9; color: #333;
    }
    .post-content h2, .post-content h3 { font-weight: 700; margin: 28px 0 14px; color: var(--text-dark); }
    .post-content img { max-width: 100%; border-radius: 12px; margin: 20px 0; }
    .post-content p { margin-bottom: 16px; }
    .post-content blockquote {
        border-left: 4px solid var(--primary);
        padding: 12px 20px;
        background: var(--primary-light);
        border-radius: 0 8px 8px 0;
        margin: 20px 0;
        font-style: italic;
    }

    .related-news-card {
        display: flex; gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-light);
        text-decoration: none;
        color: inherit;
        transition: color .2s;
    }
    .related-news-card:hover { color: var(--primary); }
    .related-news-card img {
        width: 72px; height: 72px;
        border-radius: 8px; object-fit: cover; flex-shrink: 0;
    }
    .related-news-card .title {
        font-size: .875rem; font-weight: 600; line-height: 1.45;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .related-news-card .date { font-size: .75rem; color: #aaa; margin-top: 4px; }

    .sidebar-widget {
        background: #fff; border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,.07);
        padding: 20px; margin-bottom: 20px;
    }
    .sidebar-widget h6 {
        font-size: .8rem; font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase; letter-spacing: .8px;
        margin-bottom: 14px; padding-bottom: 10px;
        border-bottom: 2px solid var(--primary-light);
    }
    .cat-list a {
        display: flex; justify-content: space-between;
        align-items: center;
        padding: 7px 0; font-size: .875rem;
        color: var(--text-dark); text-decoration: none;
        border-bottom: 1px solid var(--border-light);
        transition: color .2s;
    }
    .cat-list a:hover { color: var(--primary); font-weight: 600; }
    .cat-list a .count {
        background: var(--primary-light); color: var(--primary);
        font-size: .72rem; font-weight: 700;
        padding: 1px 8px; border-radius: 50px;
    }
</style>
@endsection

@section('content')

<div class="page-hero">
    <div class="container">
        <h1>{{ \Str::limit($post->name, 60) }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('frontend.news.index') }}">Tin tức</a></li>
                @if($post->category)
                    <li class="breadcrumb-item">{{ $post->category->name }}</li>
                @endif
                <li class="breadcrumb-item active">{{ \Str::limit($post->name, 30) }}</li>
            </ol>
        </nav>
    </div>
</div>

<section style="padding:48px 0">
    <div class="container">
        <div class="row g-5">

            {{-- NỘI DUNG CHÍNH --}}
            <div class="col-lg-8">
                <article>
                    {{-- Meta --}}
                    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                        @if($post->category)
                        <a href="{{ route('frontend.news.index', ['category' => $post->category_post_id]) }}"
                           style="display:inline-block;background:var(--primary-light);color:var(--primary);font-size:.73rem;font-weight:700;padding:3px 12px;border-radius:50px;text-decoration:none;text-transform:uppercase;letter-spacing:.5px">
                            {{ $post->category->name }}
                        </a>
                        @endif
                        <span class="text-muted" style="font-size:.82rem">
                            <i class="bi bi-calendar3 me-1"></i>{{ $post->created_at->format('d/m/Y') }}
                        </span>
                        <span class="text-muted" style="font-size:.82rem">
                            <i class="bi bi-eye me-1"></i>{{ number_format($post->view ?? 0) }} lượt xem
                        </span>
                    </div>

                    {{-- Tiêu đề --}}
                    <h1 style="font-size:1.6rem;font-weight:800;color:var(--text-dark);margin-bottom:16px;line-height:1.4">
                        {{ $post->name }}
                    </h1>

                    {{-- Mô tả ngắn --}}
                    @if($post->description)
                    <p style="font-size:1.05rem;color:#555;border-left:4px solid var(--accent);padding-left:16px;margin-bottom:24px;font-style:italic">
                        {{ $post->description }}
                    </p>
                    @endif

                    {{-- Ảnh banner --}}
                    @if($post->image)
                    <img src="{{ cdn($post->image)  }}" alt="{{ $post->name }}"
                         style="width:100%;border-radius:16px;margin-bottom:28px;box-shadow:0 6px 24px rgba(0,0,0,.1)">
                    @endif

                    {{-- Nội dung chi tiết --}}
                    @php
                        $content = $post->content;

                        // Chuyển link youtu.be thành iframe
                        $content = preg_replace_callback(
                            '~https?://youtu\.be/([a-zA-Z0-9_-]+)(?:\?[^\s<]*)?~',
                            function ($matches) {
                                $videoId = $matches[1];

                                return '<div class="youtube-video">
                                            <iframe
                                                src="https://www.youtube.com/embed/' . $videoId . '"
                                                title="YouTube video"
                                                frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                allowfullscreen>
                                            </iframe>
                                        </div>';
                            },
                            $content
                        );
                    @endphp

                    <div class="post-content">
                        {!! $content !!}
                    </div>
                </article>

                {{-- Chia sẻ --}}
                <div style="padding:20px 0;border-top:1px solid var(--border-light);border-bottom:1px solid var(--border-light);margin:32px 0;display:flex;align-items:center;gap:12px">
                    <span style="font-weight:600;font-size:.875rem">Chia sẻ:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                       target="_blank"
                       style="display:inline-flex;align-items:center;gap:6px;background:#1877F2;color:#fff;padding:6px 16px;border-radius:6px;font-size:.82rem;font-weight:600;text-decoration:none">
                        <i class="bi bi-facebook"></i> Facebook
                    </a>
                </div>

                {{-- BÀI VIẾT LIÊN QUAN --}}
                @if($related->isNotEmpty())
                <div class="mt-4">
                    <h4 style="font-weight:700;font-size:1.1rem;margin-bottom:20px">
                        <i class="bi bi-newspaper me-2" style="color:var(--primary)"></i>Bài viết liên quan
                    </h4>
                    <div class="row g-3">
                        @foreach($related as $item)
                        <div class="col-md-6">
                            <a href="{{ route('frontend.news.show', $item->slug) }}" class="related-news-card">
                                @if($item->image)
                                    <img src="{{ cdn($item->image) }}" alt="{{ $item->name }}">
                                @else
                                    <div style="width:72px;height:72px;border-radius:8px;background:var(--primary-light);flex-shrink:0;display:flex;align-items:center;justify-content:center">
                                        <i class="bi bi-newspaper" style="color:var(--primary);opacity:.4"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="title">{{ $item->name }}</div>
                                    <div class="date">{{ $item->created_at->format('d/m/Y') }}</div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- SIDEBAR --}}
            <div class="col-lg-4">
                <div style="position:sticky;top:140px">
                    {{-- Danh mục --}}
                    <div class="sidebar-widget">
                        <h6><i class="bi bi-grid me-2"></i>Danh mục</h6>
                        <div class="cat-list">
                            @foreach($categories as $cat)
                            <a href="{{ route('frontend.news.index', ['slug' => $cat->slug]) }}">
                                <span>{{ $cat->name }}</span>
                                @if($cat->posts_count)
                                    <span class="count">{{ $cat->posts_count }}</span>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- CTA --}}
                    <div class="sidebar-widget text-center" style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));color:#fff;padding:28px 20px">
                        <i class="bi bi-people-fill" style="font-size:2.5rem;opacity:.8;margin-bottom:12px;display:block"></i>
                        <h6 style="color:#fff;font-size:1rem;margin-bottom:8px">Tham gia ngay hôm nay</h6>
                        <p style="font-size:.8rem;opacity:.85;margin-bottom:16px">Xây dựng thu nhập bền vững cùng cộng đồng</p>
                        <a href="{{ route('admin.register') }}"
                           class="btn btn-warning btn-sm fw-bold px-4">Đăng ký miễn phí</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
