@extends('Core::Frontend.layouts.app')

@section('title', 'Tin tức')

@section('extra-styles')
<style>
    .page-hero {
        background: linear-gradient(135deg, #1a3a6b 0%, #2980b9 100%);
        color: #fff; padding: 48px 0 40px; text-align: center;
    }
    .page-hero h1 { font-size: 2rem; font-weight: 800; }
    .page-hero .breadcrumb { justify-content: center; background: none; padding: 0; margin: 8px 0 0; }
    .page-hero .breadcrumb-item, .page-hero .breadcrumb-item a { color: rgba(255,255,255,.75); font-size: .875rem; text-decoration: none; }
    .page-hero .breadcrumb-item.active { color: #fff; }
    .page-hero .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.5); }

    /* News card */
    .news-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,.07);
        transition: transform .25s, box-shadow .25s;
        height: 100%;
    }
    .news-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(0,0,0,.12); }
    .news-card .img-wrap {
        height: 200px; overflow: hidden;
    }
    .news-card .img-wrap img {
        width: 100%; height: 100%;
        object-fit: cover; transition: transform .35s;
    }
    .news-card:hover .img-wrap img { transform: scale(1.05); }
    .news-card .card-body { padding: 16px; }
    .news-card .cat-badge {
        display: inline-block;
        background: var(--primary-light);
        color: var(--primary);
        font-size: .73rem; font-weight: 700;
        padding: 2px 10px; border-radius: 50px;
        margin-bottom: 8px; text-transform: uppercase; letter-spacing: .5px;
    }
    .news-card .post-title {
        font-weight: 700; font-size: .9rem;
        color: var(--text-dark);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 8px; line-height: 1.5;
    }
    .news-card .post-desc {
        font-size: .82rem; color: var(--text-muted);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden; line-height: 1.55;
    }
    .news-card .post-meta {
        font-size: .78rem; color: #aaa; margin-top: 10px;
    }

    /* Featured large card */
    .news-featured {
        border-radius: 16px; overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,.1);
        position: relative;
        height: 380px;
    }
    .news-featured img {
        width: 100%; height: 100%;
        object-fit: cover;
    }
    .news-featured .overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,.8) 0%, transparent 60%);
        display: flex; align-items: flex-end;
        padding: 24px;
    }
    .news-featured .overlay h3 { color: #fff; font-weight: 700; font-size: 1.1rem; margin-bottom: 6px; }
    .news-featured .overlay p { color: rgba(255,255,255,.75); font-size: .82rem; margin: 0; }

    /* Sidebar */
    .sidebar-widget {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,.07);
        padding: 20px;
        margin-bottom: 20px;
    }
    .sidebar-widget h6 {
        font-size: .8rem; font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase; letter-spacing: .8px;
        margin-bottom: 14px;
        padding-bottom: 10px;
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
    .cat-list a:hover, .cat-list a.active { color: var(--primary); font-weight: 600; }
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
        <h1>Tin tức & Blog</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active">Tin tức</li>
            </ol>
        </nav>
    </div>
</div>

<section style="padding:48px 0">
    <div class="container">
        <div class="row g-4">

            {{-- MAIN CONTENT --}}
            <div class="col-lg-8">

                {{-- BÀI NỔI BẬT --}}
                @if($featuredPosts->isNotEmpty())
                <div class="mb-4">
                    <h5 style="font-weight:700;margin-bottom:16px">
                        <i class="bi bi-lightning-charge-fill me-2" style="color:var(--accent)"></i>Bài viết nổi bật
                    </h5>
                    <div class="row g-3">
                        @foreach($featuredPosts as $fp)
                        <div class="col-{{ $loop->first ? '12' : '6' }}">
                            <a href="{{ route('frontend.news.show', $fp->slug) }}" style="text-decoration:none">
                                <div class="news-featured" style="{{ $loop->first ? '' : 'height:200px' }}">
                                    @if($fp->image)
                                        <img src="{{ cdn($fp->image) }}" alt="{{ $fp->name }}">
                                    @else
                                        <div style="background:linear-gradient(135deg,var(--primary-light),var(--primary));width:100%;height:100%"></div>
                                    @endif
                                    <div class="overlay">
                                        <div>
                                            <h3>{{ $fp->name }}</h3>
                                            <p>{{ $fp->category->name ?? '' }} • {{ $fp->created_at->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                <hr style="border-color:var(--border-light);margin:28px 0">
                @endif

                {{-- DANH SÁCH BÀI VIẾT --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 style="font-weight:700;margin:0">Tất cả bài viết</h5>
                    <span class="text-muted" style="font-size:.875rem">{{ $posts->total() }} bài viết</span>
                </div>
                <div class="row g-4">
                    @forelse($posts as $post)
                    <div class="col-md-6">
                        <div class="news-card">
                            <div class="img-wrap">
                                @if($post->image)
                                    <img src="{{ cdn($post->image) }}" alt="{{ $post->name }}">
                                @else
                                    <div style="height:200px;background:linear-gradient(135deg,var(--primary-light),#d4edda);display:flex;align-items:center;justify-content:center">
                                        <i class="bi bi-newspaper" style="font-size:2.5rem;color:var(--primary);opacity:.3"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                @if($post->category)
                                    <span class="cat-badge">{{ $post->category->name }}</span>
                                @endif
                                <div class="post-title">
                                    <a href="{{ route('frontend.news.show', $post->slug) }}"
                                       style="color:inherit;text-decoration:none">{{ $post->name }}</a>
                                </div>
                                @if($post->description)
                                    <div class="post-desc">{{ $post->description }}</div>
                                @endif
                                <div class="post-meta d-flex justify-content-between">
                                    <span><i class="bi bi-calendar3 me-1"></i>{{ $post->created_at->format('d/m/Y') }}</span>
                                    <span><i class="bi bi-eye me-1"></i>{{ number_format($post->view ?? 0) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="bi bi-newspaper" style="font-size:3rem;opacity:.3;display:block;margin-bottom:16px"></i>
                        Chưa có bài viết nào.
                    </div>
                    @endforelse
                </div>

                {{-- PAGINATION --}}
                @if($posts->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $posts->links('pagination::bootstrap-5') }}
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
                            <a href="{{ route('frontend.news.index') }}"
                               class="{{ !request('category') ? 'active' : '' }}">
                                <span>Tất cả</span>
                                <span class="count">{{ $posts->total() }}</span>
                            </a>
                            @foreach($categories as $cat)
                            <a href="{{ route('frontend.news.index', ['slug' => $cat->slug]) }}"
                               class="{{ request('category') == $cat->slug ? 'active' : '' }}">
                                <span>{{ $cat->name }}</span>
                                @if($cat->posts_count)
                                    <span class="count">{{ $cat->posts_count }}</span>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- CTA Banner --}}
                    <div class="sidebar-widget text-center" style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));color:#fff;padding:28px 20px">
                        <i class="bi bi-people-fill" style="font-size:2.5rem;opacity:.8;margin-bottom:12px;display:block"></i>
                        <h6 style="color:#fff;font-size:1rem;margin-bottom:8px">Tham gia cộng đồng</h6>
                        <p style="font-size:.8rem;opacity:.85;margin-bottom:16px">Nhận tin tức mới nhất và ưu đãi đặc biệt</p>
                        <a href="{{ route('admin.register') }}"
                           class="btn btn-warning btn-sm fw-bold px-4">Đăng ký ngay</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
