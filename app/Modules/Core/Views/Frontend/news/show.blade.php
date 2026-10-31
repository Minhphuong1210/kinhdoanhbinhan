@extends('Core::Frontend.layouts.app')

@section('title', $post->meta_title ?: $post->name)
@section('meta_description', $post->meta_description ?: ($post->description ?: $post->name))
@if($post->image)@section('og_image', cdn($post->image))@endif

@php($shareUrl = urlencode(url()->current()))

@push('styles')
<style>
    .article { background: #fff; border: 1px solid var(--line); border-radius: 20px; padding: 32px; }
    .article h1 { font-weight: 800; font-size: 1.9rem; line-height: 1.35; }
    .article-meta { color: var(--muted); font-size: 14px; display: flex; flex-wrap: wrap; gap: 16px; padding-bottom: 18px; border-bottom: 1px solid var(--line); margin-bottom: 22px; }
    .article-cover { border-radius: 14px; overflow: hidden; margin-bottom: 22px; }
    .article-cover img { width: 100%; height: auto; display: block; }
    .article-lead { font-size: 17px; font-weight: 500; color: #33504c; border-left: 4px solid var(--primary); padding: 4px 0 4px 16px; margin-bottom: 22px; }
    .share-bar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; border-top: 1px solid var(--line); padding-top: 18px; margin-top: 28px; }
    .share-bar a, .share-bar button { width: 38px; height: 38px; border-radius: 50%; display: inline-grid; place-items: center; color: #fff; border: 0; }
    @media (max-width: 767.98px) { .article { padding: 20px; } .article h1 { font-size: 1.4rem; } }
</style>
@endpush

@section('content')
<div class="pd-breadcrumb bg-white border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 py-3 small">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('frontend.news.index') }}">Tin tức</a></li>
                @if($post->category)
                    <li class="breadcrumb-item"><a href="{{ route('frontend.news.index', ['slug' => $post->category->slug]) }}">{{ $post->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ \Illuminate\Support\Str::limit($post->name, 50) }}</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section pt-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <article class="article">
                    @if($post->category)
                        <a href="{{ route('frontend.news.index', ['slug' => $post->category->slug]) }}" class="eyebrow">{{ $post->category->name }}</a>
                    @endif
                    <h1>{{ $post->name }}</h1>
                    <div class="article-meta">
                        <span><i class="far fa-calendar-alt me-1"></i>{{ $post->created_at?->format('d/m/Y H:i') }}</span>
                        <span><i class="far fa-eye me-1"></i>{{ number_format((int) $post->view) }} lượt xem</span>
                        <span><i class="far fa-clock me-1"></i>{{ max(1, (int) ceil(count(preg_split('/\s+/u', trim(strip_tags((string) $post->content)), -1, PREG_SPLIT_NO_EMPTY)) / 220)) }} phút đọc</span>
                    </div>

                    @if($post->description)
                        <p class="article-lead">{{ strip_tags($post->description) }}</p>
                    @endif

                    @if($post->image && !\Illuminate\Support\Str::contains((string) $post->content, $post->image))
                        <div class="article-cover"><img src="{{ cdn($post->image) }}" alt="{{ $post->name }}"></div>
                    @endif

                    <div class="rich-content">{!! $post->content !!}</div>

                    <div class="share-bar">
                        <strong class="me-1">Chia sẻ:</strong>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener" style="background:#1877f2" aria-label="Chia sẻ Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://zalo.me/share?url={{ $shareUrl }}" target="_blank" rel="noopener" style="background:#0068ff;font-size:11px;font-weight:800" aria-label="Chia sẻ Zalo">Zalo</a>
                        <button type="button" style="background:var(--primary-dark)" aria-label="Sao chép liên kết"
                                onclick="navigator.clipboard.writeText(window.location.href).then(() => { this.innerHTML = '<i class=&quot;fas fa-check&quot;></i>'; })"><i class="fas fa-link"></i></button>
                    </div>
                </article>

                @if($related->isNotEmpty())
                    <div class="mt-5">
                        <div class="section-title"><div><h2>Bài viết <span>liên quan</span></h2></div></div>
                        <div class="row g-4">
                            @foreach($related as $item)
                                <div class="col-md-4">@include('Core::Frontend.news.partials.post-card', ['post' => $item])</div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <aside class="col-lg-4">
                @include('Core::Frontend.news.partials.sidebar', ['categories' => $categories, 'popularPosts' => $popularPosts, 'activeCategory' => $post->category])
            </aside>
        </div>
    </div>
</section>
@endsection
